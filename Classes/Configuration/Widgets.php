<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Configuration;

use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\ActionsPerDayRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\ActionsPerMonthRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\AiAssistantsRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\AnnotationsRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\BounceRateRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\BrowserPluginsRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\BrowsersRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\CampaignsRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\ContentNamesRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\ContentPiecesRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\ConversionsPerMonthRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\CountriesRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\CreateAnnotationRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\JavaScriptErrorsRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\LinkMatomoRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\MostViewedPagesRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\OsFamiliesRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\PagesNotFoundRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\RegistrationInterface;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\SiteSearchKeywordsRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\SiteSearchNoResultKeywordsRegstration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\VisitsPerDayRegistration;
use Brotkrueml\MatomoWidgets\DependencyInjection\Widgets\VisitsPerMonthRegistration;
use Brotkrueml\MatomoWidgets\Extension;

/**
 * @internal
 */
enum Widgets
{
    case ActionsPerDay;
    case ActionsPerMonth;
    case AiAssistants;
    case Annotations;
    case ConversionsPerMonth;
    case CreateAnnotation;
    case BounceRate;
    case BrowserPlugins;
    case Browsers;
    case Campaigns;
    case ContentNames;
    case ContentPieces;
    case Countries;
    case JavaScriptErrors;
    case LinkMatomo;
    case MostViewedPages;
    case OsFamilies;
    case PagesNotFound;
    case SiteSearchKeywords;
    case SiteSearchNoResultKeywords;
    case VisitsPerDay;
    case VisitsPerMonth;

    public function getTranslationKey(): string
    {
        return match ($this) {
            Widgets::ActionsPerDay => 'visitsSummary.actionsPerDay',
            Widgets::ActionsPerMonth => 'visitsSummary.actionsPerMonth',
            Widgets::AiAssistants => 'referrers.aiAssistants',
            Widgets::Annotations => 'annotations',
            Widgets::ConversionsPerMonth => 'goals.conversionsPerMonth',
            Widgets::CreateAnnotation => 'createAnnotation',
            Widgets::BounceRate => 'visitsSummary.bounceRate',
            Widgets::BrowserPlugins => 'devicePlugins.plugin',
            Widgets::Browsers => 'devicesDetection.browsers',
            Widgets::Campaigns => 'referrers.campaigns',
            Widgets::ContentNames => 'contents.contentNames',
            Widgets::ContentPieces => 'contents.contentPieces',
            Widgets::Countries => 'userCountry.country',
            Widgets::JavaScriptErrors => 'events.javaScriptErrors',
            Widgets::LinkMatomo => 'linkMatomo',
            Widgets::MostViewedPages => 'actions.mostViewedPages',
            Widgets::OsFamilies => 'devicesDetection.osFamilies',
            Widgets::PagesNotFound => 'actions.pagesNotFound',
            Widgets::SiteSearchKeywords => 'actions.siteSearchKeywords',
            Widgets::SiteSearchNoResultKeywords => 'actions.siteSearchNoResultKeywords',
            Widgets::VisitsPerDay => 'visitsSummary.visitsPerDay',
            Widgets::VisitsPerMonth => 'visitsSummary.visitsPerMonth',
        };
    }

    /**
     * @return array<non-empty-array<'label'|'value', string>>
     */
    public static function getItemsForSiteConfiguration(): array
    {
        $items = [];
        foreach (self::cases() as $case) {
            $items[] = [
                'label' => \sprintf(
                    Extension::LANGUAGE_PATH_DASHBOARD . ':widgets.%s.title',
                    $case->getTranslationKey(),
                ),
                'value' => \lcfirst($case->name),
            ];
        }

        return $items;
    }

    /**
     * @return class-string<RegistrationInterface>
     */
    public function getAssociatedClassNameForRegistration(): string
    {
        return match ($this) {
            Widgets::ActionsPerDay => ActionsPerDayRegistration::class,
            Widgets::ActionsPerMonth => ActionsPerMonthRegistration::class,
            Widgets::AiAssistants => AiAssistantsRegistration::class,
            Widgets::Annotations => AnnotationsRegistration::class,
            Widgets::BounceRate => BounceRateRegistration::class,
            Widgets::BrowserPlugins => BrowserPluginsRegistration::class,
            Widgets::Browsers => BrowsersRegistration::class,
            Widgets::Campaigns => CampaignsRegistration::class,
            Widgets::ContentNames => ContentNamesRegistration::class,
            Widgets::ContentPieces => ContentPiecesRegistration::class,
            Widgets::ConversionsPerMonth => ConversionsPerMonthRegistration::class,
            Widgets::CreateAnnotation => CreateAnnotationRegistration::class,
            Widgets::Countries => CountriesRegistration::class,
            Widgets::JavaScriptErrors => JavaScriptErrorsRegistration::class,
            Widgets::LinkMatomo => LinkMatomoRegistration::class,
            Widgets::MostViewedPages => MostViewedPagesRegistration::class,
            Widgets::OsFamilies => OsFamiliesRegistration::class,
            Widgets::PagesNotFound => PagesNotFoundRegistration::class,
            Widgets::SiteSearchKeywords => SiteSearchKeywordsRegistration::class,
            Widgets::SiteSearchNoResultKeywords => SiteSearchNoResultKeywordsRegstration::class,
            Widgets::VisitsPerDay => VisitsPerDayRegistration::class,
            Widgets::VisitsPerMonth => VisitsPerMonthRegistration::class,
        };
    }
}
