<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Domain\Validation;

use Brotkrueml\MatomoWidgets\Exception\ValidationException;

final class CustomDimensionConfigurationValidator
{
    private const ALLOWED_SCOPES = ['action', 'visit'];

    /**
     * @var array<string, string|int>
     */
    private array $configuration = [];
    /**
     * @var int[]
     */
    private array $alreadyDefinedIdDimensions = [];

    /**
     * @param array<string, string|int> $configuration It is intended to have no type declaration "array" as a speaking error should be returned
     */
    public function validate(mixed $configuration): bool
    {
        $this->checkConfigurationIsArray($configuration);
        $this->configuration = $configuration;
        $this->checkScope();
        $this->checkIdDimension();

        return true;
    }

    private function checkConfigurationIsArray(mixed $configuration): void
    {
        if (! \is_array($configuration)) {
            throw new ValidationException(
                \sprintf('A custom dimension configuration is not an array, "%s" given', \gettype($configuration)),
                1618591877,
            );
        }
    }

    private function checkScope(): void
    {
        if (! isset($this->configuration['scope'])) {
            throw new ValidationException(
                'A custom dimension configuration has no "scope" given',
                1618591878,
            );
        }

        if (! \in_array($this->configuration['scope'], self::ALLOWED_SCOPES, true)) {
            throw new ValidationException(
                \sprintf(
                    'A custom dimension configuration has an invalid scope given: "%s", allowed: %s',
                    $this->configuration['scope'],
                    \implode(',', self::ALLOWED_SCOPES),
                ),
                1618591879,
            );
        }
    }

    private function checkIdDimension(): void
    {
        if (! isset($this->configuration['idDimension'])) {
            throw new ValidationException(
                'A custom dimension configuration has no "idDimension" given',
                1618591880,
            );
        }

        if (! \is_numeric($this->configuration['idDimension'])) {
            throw new ValidationException(
                \sprintf(
                    'A custom dimension configuration has a non-numeric "idDimension" parameter "%s"',
                    $this->configuration['idDimension'],
                ),
                1618591881,
            );
        }

        $idDimension = (int)$this->configuration['idDimension'];
        if ($idDimension <= 0) {
            throw new ValidationException(
                \sprintf(
                    'A custom dimension configuration has an invalid "idDimension" parameter "%s"',
                    $this->configuration['idDimension'],
                ),
                1618591882,
            );
        }

        if (\in_array($idDimension, $this->alreadyDefinedIdDimensions, true)) {
            throw new ValidationException(
                \sprintf(
                    'The parameter "idDimension" with the value "%d" is already configured',
                    $idDimension,
                ),
                1618591883,
            );
        }

        $this->alreadyDefinedIdDimensions[] = $idDimension;
    }
}
