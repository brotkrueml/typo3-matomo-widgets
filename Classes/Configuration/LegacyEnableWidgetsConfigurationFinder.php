<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Configuration;

use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @implements \IteratorAggregate<array>
 * @internal
 */
class LegacyEnableWidgetsConfigurationFinder implements \IteratorAggregate, \Countable
{
    /**
     * @var string[]
     */
    private $enableWidgetsIdentifier = [
        'matomoWidgetsEnableActionsPerDay',
        'matomoWidgetsEnableActionsPerMonth',
        'matomoWidgetsEnableBounceRate',
        'matomoWidgetsEnableBrowsers',
        'matomoWidgetsEnableCampaigns',
        'matomoWidgetsEnableCountries',
        'matomoWidgetsEnableLinkMatomo',
        'matomoWidgetsEnableOsFamilies',
        'matomoWidgetsEnableVisitsPerDay',
        'matomoWidgetsEnableVisitsPerMonth',
    ];

    /**
     * @var array<string, array<string, bool>>
     */
    private $configurations = [];

    public function __construct(SiteFinder $siteFinder = null)
    {
        /** @var SiteFinder $siteFinder */
        $siteFinder = $siteFinder ?? GeneralUtility::makeInstance(SiteFinder::class);

        /** @var array<string, Site> $sites */
        $sites = $siteFinder->getAllSites();

        foreach ($sites as $siteIdentifier => $site) {
            foreach ($this->enableWidgetsIdentifier as $widgetIdentifier) {
                if (! isset($site->getConfiguration()[$widgetIdentifier])) {
                    continue;
                }

                $this->configurations[$siteIdentifier][$widgetIdentifier] = (bool)$site->getConfiguration()[$widgetIdentifier];
            }
        }
    }

    /**
     * @return string[]
     */
    public function getEnableWidgetsIdentifier(): array
    {
        return $this->enableWidgetsIdentifier;
    }

    /**
     * @return \ArrayIterator<string, array<string, bool>>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->configurations);
    }

    public function count(): int
    {
        return \count($this->configurations);
    }
}
