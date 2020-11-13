<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Updates;

use Brotkrueml\MatomoWidgets\Configuration\ConfigurationFinder;
use Brotkrueml\MatomoWidgets\Configuration\WidgetsProvider;
use Brotkrueml\MatomoWidgets\Extension;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Configuration\SiteConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\ChattyInterface;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

final class SiteConfigurationMigration implements ChattyInterface, UpgradeWizardInterface
{
    /** @var ExtensionConfiguration */
    private $extensionConfiguration;

    /** @var SiteFinder */
    private $siteFinder;

    /** @var ConfigurationFinder */
    private $configurationFinder;

    /** @var OutputInterface */
    private $output;

    public function __construct(
        ConfigurationFinder $configurationFinder = null,
        ExtensionConfiguration $extensionConfiguration = null,
        SiteFinder $siteFinder = null
    ) {
        $this->configurationFinder = $configurationFinder ?? new ConfigurationFinder(Environment::getProjectPath());
        $this->extensionConfiguration = $extensionConfiguration ?? GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $this->siteFinder = $siteFinder ?? GeneralUtility::makeInstance(SiteFinder::class);
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    public function getIdentifier(): string
    {
        return 'matomoWidgetsSiteConfigurationMigration';
    }

    public function getTitle(): string
    {
        return 'Matomo Widgets: Migrate to site configuration';
    }

    public function getDescription(): string
    {
        return 'The extension configuration is migrated to the site configuration if only one site exists '
            . 'and no site configuration for the Matomo widgets is available.';
    }

    public function executeUpdate(): bool
    {
        $newSiteData = $this->buildSiteConfiguration();

        // There is only one site configured (checked in updateNecessary())
        $site = \current($this->siteFinder->getAllSites(false));
        $newSiteConfiguration = array_merge($site->getConfiguration(), $newSiteData);
        $siteConfigurationManager = GeneralUtility::makeInstance(SiteConfiguration::class);
        $siteConfigurationManager->write($site->getIdentifier(), $newSiteConfiguration);

        $this->clearOldExtensionConfiguration();

        return true;
    }

    private function buildSiteConfiguration(): array
    {
        $oldConfiguration = $this->extensionConfiguration->get(Extension::KEY);
        $newSiteData = [
            'matomoWidgetsIdSite' => (int)$oldConfiguration['idSite'],
            'matomoWidgetsTitle' => '',
            'matomoWidgetsTokenAuth' => $oldConfiguration['tokenAuth'] ?? '',
            'matomoWidgetsUrl' => $oldConfiguration['url'],
        ];

        $widgets = (new WidgetsProvider())->getWidgetConfigurationKeys();
        foreach ($widgets as $widgetKey) {
            $newSiteData[$widgetKey] = true;
        }

        return $newSiteData;
    }

    private function clearOldExtensionConfiguration(): void
    {
        $this->extensionConfiguration->set(Extension::KEY, 'idSite', '');
        $this->extensionConfiguration->set(Extension::KEY, 'tokenAuth', '');
        $this->extensionConfiguration->set(Extension::KEY, 'url', '');
    }

    public function updateNecessary(): bool
    {
        if ($this->isAlreadySiteConfigurationAvailable()) {
            $this->output->writeln('<info>A configured site configuration is already available.</info>');
            return false;
        }

        if ($this->hasMoreThanOneDefinedSite()) {
            $this->output->writeln(' <info>There is more than one site available, please configure the according site manually.</info>');
            return false;
        }

        if ($this->isExtensionConfigurationAvailable()) {
            return true;
        }

        $this->output->writeln('<info>Extension configuration is not available, so no migration is performed.</info>');
        return false;
    }

    private function isAlreadySiteConfigurationAvailable(): bool
    {
        return \count($this->configurationFinder) > 0;
    }

    private function hasMoreThanOneDefinedSite(): bool
    {
        return \count($this->siteFinder->getAllSites()) > 1;
    }

    private function isExtensionConfigurationAvailable(): bool
    {
        $oldConfiguration = $this->extensionConfiguration->get(Extension::KEY);
        if (empty($oldConfiguration)) {
            return false;
        }

        return ($oldConfiguration['idSite'] ?? '') && ($oldConfiguration['url'] ?? '');
    }

    public function getPrerequisites(): array
    {
        return [];
    }
}
