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
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * @internal
 */
final class DecorateViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('decorator', 'object', 'The class name of the decorator', true);
        $this->registerArgument('value', 'string', 'The value to decorate', true);
    }

    public function render(): string
    {
        /** @var DecoratorInterface|null $decorator */
        $decorator = $this->arguments['decorator'];
        $value = (string) $this->arguments['value'];

        if (! $decorator instanceof DecoratorInterface) {
            throw new Exception(
                \sprintf(
                    'The decorator "%s" is not an instance of "%s"',
                    \get_debug_type($decorator),
                    DecoratorInterface::class,
                ),
                1594828163,
            );
        }

        return $decorator->decorate($value);
    }
}
