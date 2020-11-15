<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Updates;

use Brotkrueml\MatomoWidgets\Configuration\Configuration;
use Brotkrueml\MatomoWidgets\Configuration\ConfigurationFinder;
use Brotkrueml\MatomoWidgets\Domain\Repository\DashboardRepository;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\ChattyInterface;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

final class WidgetMigration implements ChattyInterface, UpgradeWizardInterface
{
    /** @var ConfigurationFinder */
    private $configurationFinder;

    /** @var DashboardRepository */
    private $dashboardRepository;

    /**
     * @var OutputInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $output;

    /** @var int */
    private $migratedDashboards = 0;

    /** @var int */
    private $migratedWidgets = 0;

    public function __construct(ConfigurationFinder $configurationFinder = null, DashboardRepository $dashboardRepository = null)
    {
        $this->configurationFinder = $configurationFinder ?? new ConfigurationFinder(Environment::getProjectPath());
        /** @psalm-suppress PropertyTypeCoercion */
        $this->dashboardRepository = $dashboardRepository ?? GeneralUtility::makeInstance(DashboardRepository::class);
        /** @psalm-suppress PropertyTypeCoercion */
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    public function getIdentifier(): string
    {
        return 'matomoWidgetsWidgetMigration';
    }

    public function getTitle(): string
    {
        return 'Matomo Widgets: Migrate widgets identifiers';
    }

    public function getDescription(): string
    {
        return 'The widget identifiers are migrated to the new identifiers derived from the site configuration, '
            . 'This upgrade wizard is executed if only one site is available.';
    }

    public function executeUpdate(): bool
    {
        /** @var Configuration $configuration */
        /** @psalm-suppress UndefinedInterfaceMethod */
        $configuration = $this->configurationFinder->getIterator()[0];
        $dashboards = $this->dashboardRepository->findAll();
        foreach ($dashboards as $dashboard) {
            $this->migrateWidgetsForDashboard(
                $configuration->getSiteIdentifier(),
                $dashboard['identifier'],
                $dashboard['widgets']
            );
        }

        $this->output->writeln(
            \sprintf(
                '<info>Migrated %d widgets in %d dashboards. Please flush the cache via Admin Tools > Maintenance.</info>',
                $this->migratedWidgets,
                $this->migratedDashboards
            )
        );

        return true;
    }

    private function migrateWidgetsForDashboard(
        string $siteIdentifier,
        string $dashboardIdentifier,
        string $widgets
    ): void {
        $decodedWidgets = \json_decode($widgets, true);
        $widgetsMigrated = false;
        foreach ($decodedWidgets as $key => $configuration) {
            if (!\str_starts_with($configuration['identifier'], 'matomo_widgets.')) {
                continue;
            }

            if (\str_starts_with($configuration['identifier'], \sprintf('matomo_widgets.%s.', $siteIdentifier))) {
                // Already migrated
                continue;
            }

            $widgetIdentifierParts = \explode('.', $configuration['identifier']);
            $newWidgetIdentifier = \implode(
                '.',
                \array_merge(
                    \array_slice($widgetIdentifierParts, 0, 1),
                    [$siteIdentifier],
                    \array_slice($widgetIdentifierParts, 1)
                )
            );

            $configuration['identifier'] = $newWidgetIdentifier;
            $decodedWidgets[$key] = $configuration;
            $widgetsMigrated = true;
            $this->migratedWidgets++;
        }

        if ($widgetsMigrated) {
            $this->dashboardRepository->updateWidgetConfig($dashboardIdentifier, $decodedWidgets);
            $this->migratedDashboards++;
        }
    }

    public function updateNecessary(): bool
    {
        return \count($this->configurationFinder) === 1;
    }

    public function getPrerequisites(): array
    {
        return [];
    }
}
