<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\ViewHelpers;

use Brotkrueml\MatomoWidgets\Widgets\Decorator\DecoratorInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper;

/**
 * @internal
 */
class DecorateViewHelper extends ViewHelper\AbstractViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('decorator', 'object', 'The class name of the decorator', true);
        $this->registerArgument('value', 'string', 'The value to decorate', true);
    }

    /**
     * @param array{decorator: DecoratorInterface|null, value: string|null} $arguments
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        $decorator = $arguments['decorator'];
        $value = (string)$arguments['value'];

        if (! $decorator instanceof DecoratorInterface) {
            throw new ViewHelper\Exception(
                \sprintf(
                    'The decorator "%s" is not an instance of "%s"',
                    \get_debug_type($decorator),
                    DecoratorInterface::class
                ),
                1594828163
            );
        }

        return $decorator->decorate($value);
    }
}
