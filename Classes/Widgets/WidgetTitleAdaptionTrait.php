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

trait WidgetTitleAdaptionTrait
{
    private function prefixWithSiteTitle(WidgetConfigurationInterface $configuration, array $options): WidgetConfigurationInterface
    {
        $title = $this->getLanguageService()->sL($options['title'] ?? '') ?: $configuration->getTitle();
        $siteTitle = $options['siteTitle'] ?? '';
        if ($siteTitle) {
            $title = \sprintf('%s: %s', $options['siteTitle'], $title);
        }

        $configuration = new WidgetConfiguration(
            $configuration->getIdentifier(),
            $configuration->getServiceName(),
            $configuration->getGroupNames(),
            $title,
            $configuration->getDescription(),
            $configuration->getIconIdentifier(),
            $configuration->getHeight(),
            $configuration->getWidth(),
            \explode(' ', $configuration->getAdditionalCssClasses())
        );

        return $configuration;
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
