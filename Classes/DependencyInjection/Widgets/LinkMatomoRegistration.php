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
use Brotkrueml\MatomoWidgets\Widgets\CtaWidget;
use Brotkrueml\MatomoWidgets\Widgets\Provider\LinkMatomoButtonProvider;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class LinkMatomoRegistration extends AbstractRegistration
{
    protected $serviceIdSuffix = 'linkMatomo';

    public function register(): void
    {
        if (! $this->matomoConfiguration->isWidgetActive('linkMatomo')) {
            return;
        }

        $this->registerDataProvider();
        $this->registerWidget();
    }

    private function registerDataProvider(): void
    {
        $this->services
            ->set($this->buildServiceDataProviderId(), LinkMatomoButtonProvider::class)
            ->arg('$link', $this->matomoConfiguration->getUrl());
    }

    private function registerWidget(): void
    {
        $localisedTitle = Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.linkMatomo.title';
        $title = $this->matomoConfiguration->getSiteTitle()
            ? \sprintf('%s: %s', $this->matomoConfiguration->getSiteTitle(), 'Link to Matomo')
            : $localisedTitle;

        $this->services
            ->set($this->buildServiceWidgetId(), CtaWidget::class)
            ->arg('$view', new Reference('dashboard.views.widget'))
            ->arg('$buttonProvider', new Reference($this->buildServiceDataProviderId()))            ->arg(
                '$options',
                [
                    'siteTitle' => $this->matomoConfiguration->getSiteTitle(),
                    'title' => $localisedTitle,
                    'text' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.linkMatomo.text',
                ]
            )
            ->tag(
                'dashboard.widget',
                [
                    'identifier' => $this->buildWidgetIdentifier(),
                    'groupNames' => 'matomo',
                    'title' => $title,
                    'description' => Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.linkMatomo.description',
                    'iconIdentifier' => self::ICON_IDENTIFIER,
                ]
            );
    }
}
