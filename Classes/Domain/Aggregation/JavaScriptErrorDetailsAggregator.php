<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Domain\Aggregation;

use Brotkrueml\MatomoWidgets\Domain\Entity\JavaScriptErrorDetails;

/**
 * @internal
 */
final class JavaScriptErrorDetailsAggregator
{
    private readonly JavaScriptErrorDetails $javaScriptDetails;

    public function __construct()
    {
        $this->javaScriptDetails = new JavaScriptErrorDetails();
    }

    /**
     * @param list<array{browserName: string, browserIcon: string, browserVersion: string, actionDetails: list<array{type: string, eventCategory: string, eventAction: string, url: string, timestamp: int}>}> $details
     */
    public function aggregate(array $details): JavaScriptErrorDetails
    {
        foreach ($details as $detail) {
            foreach ($detail['actionDetails'] as $actionDetail) {
                if ($actionDetail['type'] !== 'event') {
                    continue;
                }
                if ($actionDetail['eventCategory'] !== 'JavaScript Errors') {
                    continue;
                }

                $this->javaScriptDetails->incrementBrowserCount($detail['browserName'], $detail['browserIcon'], $detail['browserVersion']);
                $this->javaScriptDetails->incrementUrlCount($actionDetail['url']);
                $this->javaScriptDetails->incrementScriptCount($actionDetail['eventAction']);
                $this->javaScriptDetails->compareAndStoreLastAppearance($actionDetail['timestamp']);
            }
        }

        return $this->javaScriptDetails;
    }
}
