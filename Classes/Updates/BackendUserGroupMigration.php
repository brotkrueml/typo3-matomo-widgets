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
use Brotkrueml\MatomoWidgets\Domain\Repository\BackendUserGroupRepository;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\ChattyInterface;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class BackendUserGroupMigration implements ChattyInterface, UpgradeWizardInterface
{
    /** @var ConfigurationFinder */
    private $configurationFinder;

    /** @var BackendUserGroupRepository */
    private $backendUserGroupRepository;

    /**
     * @var OutputInterface
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $output;

    /** @var int */
    private $migratedBackendUserGroups = 0;

    /** @var int */
    private $migratedWidgets = 0;

    public function __construct(
        ConfigurationFinder $configurationFinder = null,
        BackendUserGroupRepository $backendUserGroupRepository = null
    ) {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->configurationFinder = $configurationFinder ?? new ConfigurationFinder(Environment::getProjectPath());
        /** @psalm-suppress PropertyTypeCoercion */
        $this->backendUserGroupRepository = $backendUserGroupRepository ?? GeneralUtility::makeInstance(BackendUserGroupRepository::class);
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    public function getIdentifier(): string
    {
        return 'matomoWidgetsBackendUserGroupMigration';
    }

    public function getTitle(): string
    {
        return 'Matomo Widgets: Migrate widgets identifiers in backend user groups';
    }

    public function getDescription(): string
    {
        return 'The widget identifiers in the backend user groups are migrated to the new identifiers derived from the site '
            . 'configuration. This upgrade wizard is executed if only one site is available.';
    }

    public function executeUpdate(): bool
    {
        /** @var Configuration $configuration */
        /** @psalm-suppress UndefinedInterfaceMethod */
        $configuration = $this->configurationFinder->getIterator()[0];
        $backendUserGroups = $this->backendUserGroupRepository->findAll();
        foreach ($backendUserGroups as $backendUserGroup) {
            if (empty($backendUserGroup['availableWidgets'])) {
                continue;
            }

            $this->migrateWidgetsForBackendUserGroup(
                $configuration->getSiteIdentifier(),
                $backendUserGroup['uid'],
                $backendUserGroup['availableWidgets']
            );
        }

        if ($this->migratedWidgets) {
            $this->output->writeln(
                \sprintf(
                    '<info>Migrated %d widgets in %d backend user groups.</info>',
                    $this->migratedWidgets,
                    $this->migratedBackendUserGroups
                )
            );
        } else {
            $this->output->writeln('<info>No widgets for migration found.</info>');
        }

        return true;
    }

    private function migrateWidgetsForBackendUserGroup(
        string $siteIdentifier,
        int $backendUserGroupUid,
        string $widgets
    ): void {
        $widgetsArray = \explode(',', $widgets);
        $widgetsMigrated = false;
        foreach ($widgetsArray as $index => $widgetIdentifier) {
            if (!\str_starts_with($widgetIdentifier, 'matomo_widgets.')) {
                continue;
            }

            if (\str_starts_with($widgetIdentifier, \sprintf('matomo_widgets.%s.', $siteIdentifier))) {
                // Already migrated
                continue;
            }

            $widgetIdentifierParts = \explode('.', $widgetIdentifier);
            $newWidgetIdentifier = \implode(
                '.',
                \array_merge(
                    \array_slice($widgetIdentifierParts, 0, 1),
                    [$siteIdentifier],
                    \array_slice($widgetIdentifierParts, 1)
                )
            );

            $widgetsArray[$index] = $newWidgetIdentifier;
            $widgetsMigrated = true;
            $this->migratedWidgets++;
        }

        if ($widgetsMigrated) {
            $this->backendUserGroupRepository->updateAvailableWidgets($backendUserGroupUid, $widgetsArray);
            $this->migratedBackendUserGroups++;
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
