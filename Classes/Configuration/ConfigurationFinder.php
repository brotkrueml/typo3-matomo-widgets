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
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationFinder implements \IteratorAggregate, \Countable
{
    /** @var array */
    private $configurations = [];

    public function __construct(string $configPath)
    {
        $finder = new Finder();
        try {
            $finder
                ->in($configPath . '/sites/*')
                ->name('config.yaml');

            foreach ($finder as $file) {
                $siteConfiguration = (new YamlFileLoader())->load($file->getRealPath());

                $url = (string)($siteConfiguration['matomoWidgetsUrl'] ?? '');
                $idSite = (int)($siteConfiguration['matomoWidgetsIdSite'] ?? 0);

                if ($url === '' || $idSite < 1) {
                    continue;
                }

                $siteTitle = $siteConfiguration['matomoWidgetsTitle'] ?? '';
                $tokenAuth = $siteConfiguration['matomoWidgetsTokenAuth'] ?? '';

                $pathSegments = \explode('/', $file->getPath());
                $siteIdentifier = \end($pathSegments);

                $activeWidgets = GeneralUtility::trimExplode(',', $siteConfiguration['matomoWidgetsActiveWidgets'], true);
                $customDimensions = $this->buildCustomDimensions($siteConfiguration['matomoWidgetsCustomDimensions'] ?? []);

                $this->configurations[] = new Configuration(
                    $siteIdentifier,
                    $siteTitle,
                    $url,
                    $idSite,
                    $tokenAuth,
                    $activeWidgets,
                    $customDimensions
                );
            }
        } catch (DirectoryNotFoundException $e) {
            // do nothing
        }
    }

    /**
     * @return CustomDimension[]
     */
    public function buildCustomDimensions(array $configurations): array
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

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->configurations);
    }

    public function count(): int
    {
        return \count($this->configurations);
    }
}
