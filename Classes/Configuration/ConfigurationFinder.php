<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Configuration;

use Brotkrueml\MatomoWidgets\Adapter\YamlFileLoader;
use Brotkrueml\MatomoWidgets\Domain\Entity\CustomDimension;
use Brotkrueml\MatomoWidgets\Domain\Validation\CustomDimensionConfigurationValidator;
use Brotkrueml\MatomoWidgets\Extension;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal
 */
final class ConfigurationFinder
{
    public static function buildConfigurations(string $configPath, bool $isMatomoIntegrationAvailable): Configurations
    {
        $yamlFileLoader = YamlFileLoader::get();
        $configurationsArray = [];
        foreach (self::getConfigurationFiles($configPath) as $file) {
            $realFile = $file->getRealPath();
            if ($realFile === false) {
                continue;
            }
            $realFile = GeneralUtility::fixWindowsFilePath($realFile);
            $siteConfiguration = $yamlFileLoader->load($realFile);

            $considerMatomoIntegration = $isMatomoIntegrationAvailable
                && ($siteConfiguration['matomoWidgetsConsiderMatomoIntegration'] ?? false);

            if ($considerMatomoIntegration) {
                $url = $siteConfiguration['matomoIntegrationUrl'] ?? '';
                $idSite = (int) ($siteConfiguration['matomoIntegrationSiteId'] ?? 0);
            } else {
                $url = $siteConfiguration['matomoWidgetsUrl'] ?? '';
                $idSite = (int) ($siteConfiguration['matomoWidgetsIdSite'] ?? 0);
            }
            if (\str_starts_with((string) $url, '//')) {
                // We have a relative URL: prepend the scheme from the base URL of the site configuration
                $url = \parse_url((string) $siteConfiguration['base'], \PHP_URL_SCHEME) . ':' . $url;
            }
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

            $siteTitle = $siteConfiguration['matomoWidgetsTitle'] ?? '';
            $tokenAuth = $siteConfiguration['matomoWidgetsTokenAuth'] ?? '';

            $pathSegments = \explode('/', $file->getPath());
            $identifier = \end($pathSegments);
            if ($identifier === Extension::ADDITIONAL_CONFIG_PATH_SEGMENT) {
                // Prefix the identifier with underscores to avoid a clash with a possible site identifier
                // (which mostly do not start with underscores)
                $identifier = '__' . \rtrim($file->getBasename($file->getExtension()), '.');
            }

            $activeWidgets = GeneralUtility::trimExplode(
                ',',
                $siteConfiguration['matomoWidgetsActiveWidgets'] ?? '',
                true,
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
                $pagesNotFoundTemplate,
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
                    ->name('config.yaml'),
            );
        } catch (DirectoryNotFoundException) {
            $siteFiles = [];
        }

        try {
            $additionalFiles = \iterator_to_array(
                Finder::create()
                    ->in($configPath . '/' . Extension::ADDITIONAL_CONFIG_PATH_SEGMENT)
                    ->name('*.yaml'),
            );
        } catch (DirectoryNotFoundException) {
            $additionalFiles = [];
        }

        return [...$siteFiles, ...$additionalFiles];
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
                (int) $configuration['idDimension'],
                (string) ($configuration['title'] ?? 'Custom Dimension ' . $configuration['idDimension']),
                (string) ($configuration['description'] ?? ''),
            );
        }

        return $customDimensions;
    }
}
