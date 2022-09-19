<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\DependencyInjection\Widgets;

use Brotkrueml\MatomoWidgets\Extension;
use Brotkrueml\MatomoWidgets\Widgets\CreateAnnotationWidget;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class CreateAnnotationRegistration extends AbstractRegistration
{
    protected $serviceIdSuffix = 'annotation.create';

    public function register(): void
    {
        if (! $this->matomoConfiguration->isWidgetActive('createAnnotation')) {
            return;
        }

        $this->registerWidget();
    }

    private function registerWidget(): void
    {
        $localisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.createAnnotation.title';
        $title = $this->matomoConfiguration->siteTitle !== ''
            ? \sprintf('%s: %s', $this->matomoConfiguration->siteTitle, 'Create annotation')
            : $localisedTitle;

        $this->services
            ->set($this->buildServiceWidgetId(), CreateAnnotationWidget::class)
            ->arg('$view', new Reference('dashboard.views.widget'))
            ->arg(
                '$options',
                [
                    'reportLink' => $this->buildReportLink(),
                    'siteIdentifier' => $this->matomoConfiguration->siteIdentifier,
                    'siteTitle' => $this->matomoConfiguration->siteTitle,
                    'title' => $localisedTitle,
                ]
            )
            ->tag(
                'dashboard.widget',
                [
                    'identifier' => $this->buildWidgetIdentifier(),
                    'groupNames' => 'matomo',
                    'title' => $title,
                    'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.createAnnotation.description',
                    'iconIdentifier' => self::ICON_IDENTIFIER,
                    'height' => 'medium',
                    'width' => 'small',
                ]
            );
    }

    private function buildReportLink(): string
    {
        return \sprintf(
            '%s?module=CoreHome&action=index&idSite=%d&period=month&date=today#?period=month&date=today&segment=&category=General_Visitors&subcategory=General_Overview',
            $this->matomoConfiguration->url,
            $this->matomoConfiguration->idSite
        );
    }
}
