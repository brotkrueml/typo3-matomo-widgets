<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Widgets;

use Brotkrueml\MatomoWidgets\Extension;
use TYPO3\CMS\Dashboard\Widgets\AdditionalJavaScriptInterface;

/**
 * @internal
 */
class JavaScriptErrorsWidget extends TableWidget implements AdditionalJavaScriptInterface
{
    /**
     * @return string[]
     */
    public function getJsFiles(): array
    {
        return [
            \sprintf('EXT:%s/Resources/Public/JavaScript/JavaScriptErrors.js', Extension::KEY),
        ];
    }
}
