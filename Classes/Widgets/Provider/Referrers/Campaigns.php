<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets\Provider\Referrers;

use Brotkrueml\MatomoWidgets\Domain\Repository\ReferrersRepository;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;

class Campaigns implements ListDataProviderInterface
{
    /** @var FrontendInterface */
    private $cache;

    /** @var LanguageService */
    private $languageService;

    /** @var ReferrersRepository */
    private $referrersRepository;

    /** @var string */
    private $period;

    /** @var string */
    private $date;

    /** @var int */
    private $maxItemsBeforeCombineOthers;

    public function __construct(
        FrontendInterface $cache,
        LanguageService $languageService,
        ReferrersRepository $referrersRepository,
        string $period,
        string $date
    ) {
        $this->cache = $cache;
        $this->languageService = $languageService;
        $this->referrersRepository = $referrersRepository;
        $this->period = $period;
        $this->date = $date;
    }

    public function getItems(): array
    {
        $cacheIdentifier = 'ReferrersCampaign';
        $campaigns = $this->cache->get($cacheIdentifier);

        if (false === $campaigns) {
            $campaigns = $this->getCampaigns();
            $this->cache->set($cacheIdentifier, $campaigns);
        }

        return $campaigns;
    }

    private function getCampaigns(): array
    {
        $campaigns = $this->referrersRepository->getCampaigns($this->period, $this->date);

        \array_walk($campaigns, static function (array &$campaign): void {
            $campaign = [
                'name' => $campaign['label'],
                'count' => $campaign['nb_visits'],
            ];
        });

        return $campaigns;
    }
}
