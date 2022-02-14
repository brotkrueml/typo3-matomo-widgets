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
 * @implements \IteratorAggregate<Configuration>
 * @internal
 */
class ConfigurationFinder implements \IteratorAggregate, \Countable
{
    /**
     * @var Configuration[]
     */
    private $configurations = [];

    public function __construct(string $configPath, bool $isMatomoIntegrationAvailable)
    {
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
                    $url = (string)($siteConfiguration['matomoIntegrationUrl'] ?? '');
                    $idSite = (int)($siteConfiguration['matomoIntegrationSiteId'] ?? 0);
                } else {
                    $url = (string)($siteConfiguration['matomoWidgetsUrl'] ?? '');
                    $idSite = (int)($siteConfiguration['matomoWidgetsIdSite'] ?? 0);
                }

                if ($considerMatomoIntegration && \str_contains($siteConfiguration['matomoIntegrationOptions'] ?? '', 'trackErrorPages')) {
                    $pagesNotFoundTemplate = (string)($siteConfiguration['matomoIntegrationErrorPagesTemplate'] ?? '');
                } else {
                    $pagesNotFoundTemplate = (string)($siteConfiguration['matomoWidgetsPagesNotFoundTemplate'] ?? '');
                }

                if ($url === '' || $idSite < 1) {
                    continue;
                }

                $siteTitle = $siteConfiguration['matomoWidgetsTitle'] ?? '';
                $tokenAuth = $siteConfiguration['matomoWidgetsTokenAuth'] ?? '';

                $pathSegments = \explode('/', $file->getPath());
                $siteIdentifier = \end($pathSegments);

                $activeWidgets = GeneralUtility::trimExplode(',', $siteConfiguration['matomoWidgetsActiveWidgets'] ?? '', true);
                $customDimensions = $this->buildCustomDimensions($siteConfiguration['matomoWidgetsCustomDimensions'] ?? []);

                $this->configurations[] = new Configuration(
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
    }

    /**
     * @param list<array{scope: string, idDimension: int|string, title?: string, description?: string}> $configurations
     * @return CustomDimension[]
     */
    private function buildCustomDimensions(array $configurations): array
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

    /**
     * @return \ArrayIterator<int, Configuration>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->configurations);
    }

    public function count(): int
    {
        return \count($this->configurations);
    }
}
