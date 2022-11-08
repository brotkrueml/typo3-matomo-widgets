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
use Brotkrueml\MatomoWidgets\Extension;
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
        foreach (self::getConfigurationFiles($configPath) as $file) {
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
            $identifier = \end($pathSegments);
            if ($identifier === Extension::ADDITIONAL_CONFIG_PATH_SEGMENT) {
                // Prefix the identifier with underscores to avoid a clash with a possible site identifier
                // (which mostly do not start with underscores)
                $identifier = '__' . \rtrim($file->getBasename($file->getExtension()), '.');
            }

            $activeWidgets = GeneralUtility::trimExplode(
                ',',
                self::resolveEnvironmentVariable($siteConfiguration['matomoWidgetsActiveWidgets'] ?? ''),
                true
            );
            $customDimensions = self::buildCustomDimensions($siteConfiguration['matomoWidgetsCustomDimensions'] ?? []);

            $configurationsArray[] = new Configuration(
                $identifier,
                $siteTitle,
                $url,
                $idSite,
                $tokenAuth,
                $activeWidgets,
                $customDimensions,
                $pagesNotFoundTemplate
            );
        }

        return new Configurations($configurationsArray);
    }

    /**
     * @return \SplFileInfo[]
     */
    private static function getConfigurationFiles(string $configPath): array
    {
        try {
            $siteFiles = \iterator_to_array(
                Finder::create()
                    ->in($configPath . '/sites/*')
                    ->name('config.yaml')
            );
        } catch (DirectoryNotFoundException $e) {
            $siteFiles = [];
        }

        try {
            $additionalFiles = \iterator_to_array(
                Finder::create()
                    ->in($configPath . '/' . Extension::ADDITIONAL_CONFIG_PATH_SEGMENT)
                    ->name('*.yaml')
            );
        } catch (DirectoryNotFoundException $e) {
            $additionalFiles = [];
        }

        return \array_merge($siteFiles, $additionalFiles);
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
