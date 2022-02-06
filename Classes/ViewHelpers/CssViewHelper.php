<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * @internal
 */
final class CssViewHelper extends AbstractTagBasedViewHelper
{
    protected $tagName = 'link';

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument(
            'path',
            'string',
            'Specify path to the asset',
            true
        );
    }

    public function render(): string
    {
        $absolutePath = GeneralUtility::getFileAbsFileName($this->arguments['path']);
        $streamlinedFilePath = PathUtility::getAbsoluteWebPath($absolutePath);

        $this->tag->addAttribute('href', $streamlinedFilePath);
        $this->tag->addAttribute('rel', 'stylesheet');

        return $this->tag->render();
    }
}
