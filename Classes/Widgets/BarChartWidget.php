<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets;

use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class BarChartWidget extends \TYPO3\CMS\Dashboard\Widgets\BarChartWidget
{
    use WidgetTitleAdaptionTrait;

    /**
     * @param array<string, string> $options
     */
    public function __construct(
        WidgetConfigurationInterface $configuration,
        ChartDataProviderInterface $dataProvider,
        StandaloneView $view,
        ?ButtonProviderInterface $buttonProvider = null,
        array $options = []
    ) {
        $configuration = $this->prefixWithSiteTitle($configuration, $options);
        $view->assign('reportLink', $options['reportLink'] ?? '');
        parent::__construct($configuration, $dataProvider, $view, $buttonProvider, $options);
    }
}
