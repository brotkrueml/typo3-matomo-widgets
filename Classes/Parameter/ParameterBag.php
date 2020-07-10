<?php
declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Parameter;

use Brotkrueml\MatomoWidgets\Exception\ParameterNotFoundException;

final class ParameterBag
{
    /**
     * @var array<string,string>
     */
    private $parameters = [];

    public function __construct(array $parameters = [])
    {
        $this->add($parameters);
    }

    public function add(array $parameters): self
    {
        \array_walk($parameters, function ($value, $name) {
            $this->set($name, $value);
        });

        return $this;
    }

    public function set(string $name, string $value): self
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    public function get(string $name): string
    {
        if (!\array_key_exists($name, $this->parameters)) {
            throw new ParameterNotFoundException(
                \sprintf('Parameter "%s" not found', $name),
                1594369628
            );
        }

        return $this->parameters[$name];
    }

    public function buildQuery(): string
    {
        return \http_build_query($this->parameters);
    }
}
