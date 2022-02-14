<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Updates;

use Brotkrueml\MatomoWidgets\Configuration\LegacyEnableWidgetsConfigurationFinder;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Configuration\SiteConfiguration;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\ChattyInterface;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * @internal
 */
final class EnableWidgetsSiteConfigurationMigration implements ChattyInterface, UpgradeWizardInterface
{
    /**
     * @var LegacyEnableWidgetsConfigurationFinder
     */
    private $legacyConfigurationFinder;

    /**
     * @var SiteFinder
     */
    private $siteFinder;

    /**
     * @var SiteConfiguration
     */
    private $siteConfiguration;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(
        LegacyEnableWidgetsConfigurationFinder $legacyConfigurationFinder = null,
        SiteFinder $siteFinder = null,
        SiteConfiguration $siteConfiguration = null
    ) {
        $this->legacyConfigurationFinder = $legacyConfigurationFinder ?? new LegacyEnableWidgetsConfigurationFinder();
        $this->siteFinder = $siteFinder ?? GeneralUtility::makeInstance(SiteFinder::class);
        $this->siteConfiguration = $siteConfiguration ?? GeneralUtility::makeInstance(SiteConfiguration::class);
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    public function getIdentifier(): string
    {
        return 'matomoWidgetsEnableWidgetsSiteConfigurationMigration';
    }

    public function getTitle(): string
    {
        return 'Matomo Widgets: Migrate enabled widgets in site configuration';
    }

    public function getDescription(): string
    {
        return 'The enabled widgets setting in site configurations is migrated to the new format';
    }

    public function executeUpdate(): bool
    {
        foreach ($this->legacyConfigurationFinder as $siteIdentifier => $enableWidgetsConfiguration) {
            $newActiveWidgetsConfiguration = $this->buildActiveWidgetsConfiguration($enableWidgetsConfiguration);
            $site = $this->siteFinder->getSiteByIdentifier($siteIdentifier);
            $newSiteConfiguration = $this->rebuildSiteConfiguration($site->getConfiguration(), $newActiveWidgetsConfiguration);
            $this->siteConfiguration->write($siteIdentifier, $newSiteConfiguration);

            $this->output->writeln(\sprintf(
                '<info>Updated Matomo Widgets configuration for site "%s"</info>',
                $site->getIdentifier()
            ));
        }

        return true;
    }

    /**
     * @param array<string, bool> $enableWidgets
     */
    private function buildActiveWidgetsConfiguration(array $enableWidgets): string
    {
        $activeWidgets = [];
        foreach ($enableWidgets as $legacyWidgetIdentifier => $isEnabled) {
            if ($isEnabled) {
                $activeWidgets[] = \lcfirst(\str_replace('matomoWidgetsEnable', '', $legacyWidgetIdentifier));
            }
        }

        return \implode(',', $activeWidgets);
    }

    /**
     * @param array<string, mixed> $siteConfiguration
     * @return array<string, mixed>
     */
    private function rebuildSiteConfiguration(array $siteConfiguration, string $newActiveWidgetsConfiguration): array
    {
        foreach ($this->legacyConfigurationFinder->getEnableWidgetsIdentifier() as $enableWidgetsIdentifier) {
            unset($siteConfiguration[$enableWidgetsIdentifier]);
        }

        $siteConfiguration['matomoWidgetsActiveWidgets'] = $newActiveWidgetsConfiguration;

        return $siteConfiguration;
    }

    public function updateNecessary(): bool
    {
        if (\count($this->legacyConfigurationFinder) > 0) {
            $this->output->writeln(\sprintf(
                '<info>%d old Matomo Widgets configuration(s) found. Update is necessary.</info>',
                \count($this->legacyConfigurationFinder)
            ));

            return true;
        }

        $this->output->writeln('<info>No old Matomo Widgets configuration(s) found. No update necessary.</info>');

        return false;
    }

    public function getPrerequisites(): array
    {
        return [];
    }
}
