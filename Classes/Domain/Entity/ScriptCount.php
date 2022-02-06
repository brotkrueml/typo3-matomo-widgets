<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Domain\Entity;

/**
 * @internal
 */
final class ScriptCount
{
    /**
     * @var string
     */
    private $script;

    /**
     * @var string
     */
    private $line;

    /**
     * @var string
     */
    private $column;

    /**
     * @var int
     */
    private $hits = 0;

    public function __construct(string $fullScriptUrlWithLineAndColumn)
    {
        $parts = \explode(':', $fullScriptUrlWithLineAndColumn);
        $this->script = $parts[0] . ':' . $parts[1];
        $this->line = $parts[2] ?? '-';
        $this->column = $parts[3] ?? '-';
    }

    public function incrementHits(): void
    {
        $this->hits++;
    }

    public function getScript(): string
    {
        return $this->script;
    }

    public function getLine(): string
    {
        return $this->line;
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getHits(): int
    {
        return $this->hits;
    }
}
