<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Configuration;

use Brotkrueml\MatomoWidgets\Domain\Entity\CustomDimension;
use Brotkrueml\MatomoWidgets\Domain\Validation\CustomDimensionConfigurationValidator;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal
 */
final class ConfigurationFinder
{
    /**
     * Regex pattern for allowed characters in environment variables
     * @see https://regex101.com/r/hIXvef/1
     * @see https://stackoverflow.com/questions/2821043/allowed-characters-in-linux-environment-variable-names
     */
    private const ENV_VAR_REGEX = '/^%env\(([a-zA-Z_]\w*)\)%$/';

    public static function buildConfigurations(string $configPath, bool $isMatomoIntegrationAvailable): Configurations
    {
        $configurationsArray = [];
        $finder = new Finder();
        try {
            $finder
                ->in($configPath . '/sites/*')
                ->name('config.yaml');

            foreach ($finder as $file) {
                $realFile = $file->getRealPath();
                if ($realFile === false) {
                    continue;
                }
                $siteConfiguration = Yaml::parseFile($realFile);

                $considerMatomoIntegration = $isMatomoIntegrationAvailable
                    && ($siteConfiguration['matomoWidgetsConsiderMatomoIntegration'] ?? false);

                if ($considerMatomoIntegration) {
                    $url = $siteConfiguration['matomoIntegrationUrl'] ?? '';
                    $idSite = (string)($siteConfiguration['matomoIntegrationSiteId'] ?? 0);
                } else {
                    $url = $siteConfiguration['matomoWidgetsUrl'] ?? '';
                    $idSite = (string)($siteConfiguration['matomoWidgetsIdSite'] ?? 0);
                }
                $url = self::resolveEnvironmentVariable($url);
                $idSite = (int)self::resolveEnvironmentVariable($idSite);
                if ($url === '') {
                    continue;
                }
                if ($idSite < 1) {
                    continue;
                }

                if ($considerMatomoIntegration && \str_contains($siteConfiguration['matomoIntegrationOptions'] ?? '', 'trackErrorPages')) {
                    $pagesNotFoundTemplate = $siteConfiguration['matomoIntegrationErrorPagesTemplate'] ?? '';
                } else {
                    $pagesNotFoundTemplate = $siteConfiguration['matomoWidgetsPagesNotFoundTemplate'] ?? '';
                }
                $pagesNotFoundTemplate = self::resolveEnvironmentVariable($pagesNotFoundTemplate);

                $siteTitle = self::resolveEnvironmentVariable($siteConfiguration['matomoWidgetsTitle'] ?? '');
                $tokenAuth = self::resolveEnvironmentVariable($siteConfiguration['matomoWidgetsTokenAuth'] ?? '');

                $pathSegments = \explode('/', $file->getPath());
                $siteIdentifier = \end($pathSegments);

                $activeWidgets = GeneralUtility::trimExplode(
                    ',',
                    self::resolveEnvironmentVariable($siteConfiguration['matomoWidgetsActiveWidgets'] ?? ''),
                    true
                );
                $customDimensions = self::buildCustomDimensions($siteConfiguration['matomoWidgetsCustomDimensions'] ?? []);

                $configurationsArray[] = new Configuration(
                    $siteIdentifier,
                    $siteTitle,
                    $url,
                    $idSite,
                    $tokenAuth,
                    $activeWidgets,
                    $customDimensions,
                    $pagesNotFoundTemplate
                );
            }
        } catch (DirectoryNotFoundException $e) {
            // do nothing
        }

        return new Configurations($configurationsArray);
    }

    /**
     * This method is necessary as environment variables are not resolved when configuration
     * is available in controllers.
     */
    private static function resolveEnvironmentVariable(string $value): string
    {
        if (\preg_match(self::ENV_VAR_REGEX, $value, $matches) !== 1) {
            return $value;
        }

        $resolvedValue = \getenv($matches[1]);
        if ($resolvedValue === false) {
            return '';
        }

        return $resolvedValue;
    }

    /**
     * @param list<array{scope: string, idDimension: int|string, title?: string, description?: string}> $configurations
     * @return CustomDimension[]
     */
    private static function buildCustomDimensions(array $configurations): array
    {
        $validator = new CustomDimensionConfigurationValidator();
        $customDimensions = [];
        foreach ($configurations as $configuration) {
            $validator->validate($configuration);
            $customDimensions[] = new CustomDimension(
                $configuration['scope'],
                (int)$configuration['idDimension'],
                (string)($configuration['title'] ?? 'Custom Dimension ' . $configuration['idDimension']),
                (string)($configuration['description'] ?? '')
            );
        }

        return $customDimensions;
    }
}
