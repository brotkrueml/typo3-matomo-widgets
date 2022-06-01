<?php

declare (strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $config): void {
    $header = <<<HEADER
This file is part of the "matomo_widgets" extension for TYPO3 CMS.

For the full copyright and license information, please read the
LICENSE.txt file that was distributed with this source code.
HEADER;

    $config->import(__DIR__ . '/.Build/vendor/brotkrueml/coding-standards/config/common.php');

    $config->paths([
        __DIR__ . '/Classes',
        __DIR__ . '/Configuration',
        __DIR__ . '/Tests',
    ]);
    $config->ruleWithConfiguration(\PhpCsFixer\Fixer\Comment\HeaderCommentFixer::class, [
        'comment_type' => 'comment',
        'header' => $header,
        'separate' => 'both',
    ]);
    $config->skip([
        \PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer::class => [
            __DIR__ . '/Classes/Extension.php',
        ],
        \PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer::class => [
            __DIR__ . '/Configuration/SiteConfiguration/Overrides/sites.php',
        ],
        \PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff::class . '.FoundInWhileCondition' => [
            __DIR__ . '/Classes/Domain/Repository/BackendUserGroupRepository',
            __DIR__ . '/Classes/Domain/Repository/DashboardRepository.php',
        ],
    ]);
};
