<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets;

use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfiguration;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;

/**
 * @internal
 */
trait WidgetTitleAdaptionTrait
{
    /**
     * @param array<string, string> $options
     */
    private function prefixWithSiteTitle(WidgetConfigurationInterface $configuration, array $options): WidgetConfigurationInterface
    {
        $title = $this->getLanguageService()->sL($options['title'] ?? '') ?: $configuration->getTitle();
        $siteTitle = $options['siteTitle'] ?? '';
        if ($siteTitle !== '') {
            $title = \sprintf('%s: %s', $siteTitle, $title);
        }

        // @phpstan-ignore-next-line Class TYPO3\CMS\Dashboard\Widgets\WidgetConfiguration constructor invoked with 9 parameters, 8 required.
        return new WidgetConfiguration(
            $configuration->getIdentifier(),
            $configuration->getServiceName(),
            $configuration->getGroupNames(),
            $title,
            $configuration->getDescription(),
            $configuration->getIconIdentifier(),
            $configuration->getHeight(),
            $configuration->getWidth(),
            // @todo Remove 9th argument when compatibility with TYPO3 v12 is dropped
            [],
        );
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
