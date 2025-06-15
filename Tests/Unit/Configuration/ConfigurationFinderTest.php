<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Configuration;

use Brotkrueml\MatomoWidgets\Configuration\Configuration;
use Brotkrueml\MatomoWidgets\Configuration\ConfigurationFinder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use TYPO3\CMS\Core\Configuration\Processor\Placeholder\EnvVariableProcessor;
use TYPO3\CMS\Core\Configuration\Processor\Placeholder\ValueFromReferenceArrayProcessor;
use TYPO3\CMS\Core\Core\ApplicationContext;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[CoversClass(ConfigurationFinder::class)]
final class ConfigurationFinderTest extends TestCase
{
    private static string $configPath;

    protected function setUp(): void
    {
        // Configuration from EXT:core/Configuration/DefaultConfiguration.php
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['yamlLoader']['placeholderProcessors'] = [
            EnvVariableProcessor::class => [],
            ValueFromReferenceArrayProcessor::class => [
                'after' => [
                    EnvVariableProcessor::class,
                ],
            ],
        ];

        $logManagerDummy = self::createStub(LogManager::class);
        $logManagerDummy
            ->method('getLogger')
            ->willReturn(new NullLogger());

        GeneralUtility::setSingletonInstance(LogManager::class, $logManagerDummy);
    }

    public static function setUpBeforeClass(): void
    {
        Environment::initialize(
            new ApplicationContext('Testing'),
            false,
            true,
            '/tmp',
            '',
            '',
            '',
            '',
            '',
        );

        self::$configPath = \realpath(\sys_get_temp_dir()) . \DIRECTORY_SEPARATOR . 'matomo_widgets_configuration_finder';

        if (\is_dir(self::$configPath)) {
            self::cleanUpDirectories();
        } else {
            \mkdir(self::$configPath);
        }
    }

    private static function cleanUpDirectories(): void
    {
        $paths = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(self::$configPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($paths as $path) {
            if ($path->isDir()) {
                if ($path->isLink()) {
                    @\unlink($path->getPathname());
                } else {
                    @\rmdir($path->getPathname());
                }
            } else {
                @\unlink($path->getPathname());
            }
        }
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_CONF_VARS']['SYS']['yamlLoader']['placeholderProcessors']);
        GeneralUtility::purgeInstances();
        self::cleanUpDirectories();
    }

    #[Test]
    public function noSiteConfigurationFoundThenNoMatomoConfigurationExists(): void
    {
        $configurations = ConfigurationFinder::buildConfigurations(self::$configPath, false);

        self::assertEmpty($configurations);
    }

    #[Test]
    public function siteConfigurationWithNoMatomoConfigurationIsNotTakenIntoAccount(): void
    {
        $this->createSiteConfiguration('some_site', [
            'rootPageId' => 1,
        ]);
        $configurations = ConfigurationFinder::buildConfigurations(self::$configPath, false);

        self::assertEmpty($configurations);
    }

    #[Test]
    public function siteConfigurationWithEmptyMatomoConfigurationIsNotTakenIntoAccount(): void
    {
        $configuration = [
            'matomoWidgetsIdSite' => 0,
            'matomoWidgetsTitle' => '',
            'matomoWidgetsTokenAuth' => '',
            'matomoWidgetsUrl' => '',
        ];
        $this->createSiteConfiguration('some_site', $configuration);
        $configurations = ConfigurationFinder::buildConfigurations(self::$configPath, false);

        self::assertEmpty($configurations);
    }

    #[Test]
    public function siteConfigurationWithAvailableMatomoConfigurationIsTakenIntoAccount(): void
    {
        $configuration = [
            'matomoWidgetsIdSite' => 42,
            'matomoWidgetsTitle' => 'Some Title',
            'matomoWidgetsTokenAuth' => 'some token',
            'matomoWidgetsUrl' => 'https://example.org/',
            'matomoWidgetsActiveWidgets' => 'actionsPerDay',
            'matomoWidgetsPagesNotFoundTemplate' => 'some 404 | {path} | {referrer}',
        ];
        $this->createSiteConfiguration('some_site', $configuration);
        $configurations = ConfigurationFinder::buildConfigurations(self::$configPath, false);

        self::assertCount(1, $configurations);

        /** @var Configuration $actualConfiguration */
        $actualConfiguration = $configurations->getIterator()->current();
        self::assertInstanceOf(Configuration::class, $actualConfiguration);
        self::assertSame('some_site', $actualConfiguration->siteIdentifier);
        self::assertSame(42, $actualConfiguration->idSite);
        self::assertSame('Some Title', $actualConfiguration->siteTitle);
        self::assertSame('some token', $actualConfiguration->tokenAuth);
        self::assertSame('https://example.org/', $actualConfiguration->url);
        self::assertSame('some 404 | {path} | {referrer}', $actualConfiguration->pagesNotFoundTemplate);
        self::assertTrue($actualConfiguration->isWidgetActive('actionsPerDay'));
        self::assertFalse($actualConfiguration->isWidgetActive('actionsPerMonth'));
        self::assertFalse($actualConfiguration->isWidgetActive('notDefined'));
    }

    #[Test]
    public function siteConfigurationWithAvailableMatomoConfigurationButMissingIdSiteIsNotTakenIntoAccount(): void
    {
        $configuration = [
            'matomoWidgetsIdSite' => 0,
            'matomoWidgetsTitle' => 'Some Title',
            'matomoWidgetsTokenAuth' => '',
            'matomoWidgetsUrl' => 'https://example.org/',
        ];
        $this->createSiteConfiguration('some_site', $configuration);
        $configurations = ConfigurationFinder::buildConfigurations(self::$configPath, false);

        self::assertEmpty($configurations);
    }

    #[Test]
    public function siteConfigurationWithAvailableMatomoConfigurationButMissingUrlIsNotTakenIntoAccount(): void
    {
        $configuration = [
            'matomoWidgetsIdSite' => 42,
            'matomoWidgetsTitle' => 'Some Title',
            'matomoWidgetsTokenAuth' => '',
            'matomoWidgetsUrl' => '',
        ];
        $this->createSiteConfiguration('some_site', $configuration);
        $configurations = ConfigurationFinder::buildConfigurations(self::$configPath, false);

        self::assertEmpty($configurations);
    }

    #[Test]
    public function siteConfigurationWhichImportsMatomoConfiguration(): void
    {
        $path = self::$configPath . '/sites/some_site/imports';
        \mkdir($path, 0777, true);

        $siteConfiguration = <<< SITE_CONFIGURATION
imports:
  - resource: "imports/widgets.yaml"
SITE_CONFIGURATION;
        \file_put_contents(\dirname($path) . '/config.yaml', $siteConfiguration);

        $widgetsConfiguration = [
            'matomoWidgetsIdSite' => 42,
            'matomoWidgetsUrl' => 'https://example.org/',
        ];
        \file_put_contents($path . '/widgets.yaml', $this->buildConfiguration($widgetsConfiguration));

        $configurations = ConfigurationFinder::buildConfigurations(self::$configPath, false);
        $actualConfiguration = $configurations->getIterator()->current();

        self::assertSame(42, $actualConfiguration->idSite);
        self::assertSame('https://example.org/', $actualConfiguration->url);
    }

    #[Test]
    public function siteConfigurationFileIsSymlinked(): void
    {
        $configuration = [
            'matomoWidgetsIdSite' => 42,
            'matomoWidgetsTitle' => 'Some Title',
            'matomoWidgetsTokenAuth' => 'some token',
            'matomoWidgetsUrl' => 'https://example.org/',
            'matomoWidgetsActiveWidgets' => 'actionsPerDay',
        ];

        $path = self::$configPath . '/sites/some_site';
        \mkdir($path, 0777, true);
        \file_put_contents($path . '/config_file.yaml', $this->buildConfiguration($configuration));
        \symlink($path . '/config_file.yaml', $path . '/config.yaml');

        $configurations = ConfigurationFinder::buildConfigurations(self::$configPath, false);

        self::assertCount(1, $configurations);

        /** @var Configuration $actualConfiguration */
        $actualConfiguration = $configurations->getIterator()->current();
        self::assertInstanceOf(Configuration::class, $actualConfiguration);
        self::assertSame('some_site', $actualConfiguration->siteIdentifier);
        self::assertSame(42, $actualConfiguration->idSite);
        self::assertSame('Some Title', $actualConfiguration->siteTitle);
        self::assertSame('some token', $actualConfiguration->tokenAuth);
        self::assertSame('https://example.org/', $actualConfiguration->url);
        self::assertTrue($actualConfiguration->isWidgetActive('actionsPerDay'));
    }

    #[Test]
    public function siteConfigurationWithAvailableMatomoConfigurationAndUninstalledMatomoIntegrationAndConsiderMatomoIntegrationEnabled(): void
    {
        $configuration = [
            'matomoWidgetsIdSite' => 42,
            'matomoWidgetsTitle' => 'Some Title',
            'matomoWidgetsTokenAuth' => '',
            'matomoWidgetsUrl' => 'https://example.org/',
            'matomoWidgetsPagesNotFoundTemplate' => 'matomo widgets 404 | {path} | {referrer}',
            'matomoWidgetsConsiderMatomoIntegration' => true,
            'matomoIntegrationUrl' => 'https://example.com/',
            'matomoIntegrationSiteId' => 1,
            'matomoIntegrationErrorPagesTemplate' => 'matomo integration 404 | {path} | {referrer}',
        ];
        $this->createSiteConfiguration('some_site', $configuration);
        $configurations = ConfigurationFinder::buildConfigurations(self::$configPath, false);

        $actualConfiguration = $configurations->getIterator()->current();
        self::assertSame(42, $actualConfiguration->idSite);
        self::assertSame('https://example.org/', $actualConfiguration->url);
        self::assertSame('matomo widgets 404 | {path} | {referrer}', $actualConfiguration->pagesNotFoundTemplate);
    }

    #[Test]
    public function siteConfigurationWithAvailableMatomoConfigurationAndInstalledMatomoIntegrationAndConsiderMatomoIntegrationEnabled(): void
    {
        $configuration = [
            'matomoWidgetsIdSite' => 42,
            'matomoWidgetsTitle' => 'Some Title',
            'matomoWidgetsTokenAuth' => '',
            'matomoWidgetsUrl' => 'https://example.org/',
            'matomoWidgetsPagesNotFoundTemplate' => 'matomo widgets 404 | {path} | {referrer}',
            'matomoWidgetsConsiderMatomoIntegration' => true,
            'matomoIntegrationUrl' => 'https://example.com/',
            'matomoIntegrationSiteId' => 1,
            'matomoIntegrationOptions' => 'linkTracking,trackErrorPages',
            'matomoIntegrationErrorPagesTemplate' => 'matomo integration 404 | {path} | {referrer}',
        ];
        $this->createSiteConfiguration('some_site', $configuration);
        $configurations = ConfigurationFinder::buildConfigurations(self::$configPath, true);

        self::assertCount(1, $configurations);

        /** @var Configuration $actualConfiguration */
        $actualConfiguration = $configurations->getIterator()->current();
        self::assertSame(1, $actualConfiguration->idSite);
        self::assertSame('https://example.com/', $actualConfiguration->url);
        self::assertSame('matomo integration 404 | {path} | {referrer}', $actualConfiguration->pagesNotFoundTemplate);
    }

    #[Test]
    public function siteConfigurationWithAvailableMatomoConfigurationAndInstalledMatomoIntegrationAndConsiderMatomoIntegrationEnabledButWithoutMatomoIntegrationConfiguration(): void
    {
        $configuration = [
            'matomoWidgetsIdSite' => 42,
            'matomoWidgetsTitle' => 'Some Title',
            'matomoWidgetsTokenAuth' => '',
            'matomoWidgetsUrl' => 'https://example.org/',
            'matomoWidgetsConsiderMatomoIntegration' => true,
        ];
        $this->createSiteConfiguration('some_site', $configuration);
        $configurations = ConfigurationFinder::buildConfigurations(self::$configPath, true);

        self::assertEmpty($configurations);
    }

    #[Test]
    public function siteConfigurationWithAvailableMatomoConfigurationAndInstalledMatomoIntegrationAndConsiderMatomoIntegrationEnabledButWithoutMatomoTrackErrorPagesActivated(): void
    {
        $configuration = [
            'matomoWidgetsIdSite' => 42,
            'matomoWidgetsTokenAuth' => '',
            'matomoWidgetsPagesNotFoundTemplate' => 'matomo widgets 404 | {path} | {referrer}',
            'matomoWidgetsConsiderMatomoIntegration' => true,
            'matomoIntegrationUrl' => 'https://example.com/',
            'matomoIntegrationSiteId' => 1,
            'matomoIntegrationOptions' => 'linkTracking',
            'matomoIntegrationErrorPagesTemplate' => 'matomo integration 404 | {path} | {referrer}',
        ];
        $this->createSiteConfiguration('some_site', $configuration);
        $configurations = ConfigurationFinder::buildConfigurations(self::$configPath, true);

        $actualConfiguration = $configurations->getIterator()->current();
        self::assertCount(1, $configurations);
        self::assertSame('matomo widgets 404 | {path} | {referrer}', $actualConfiguration->pagesNotFoundTemplate);
    }

    #[Test]
    public function environmentVariablesAreResolvedCorrectly(): void
    {
        \putenv('SOME_TITLE=The resolved title');
        \putenv('SOME_URL=The resolved URL');
        \putenv('SOME_ID=42');
        \putenv('SOME_TOKEN=The resolved token');
        \putenv('SOME_ACTIVE_WIDGETS=widget1,widget2');
        \putenv('SOME_ERROR_PAGES_TEMPLATE=The resolved pages not found template');
        $configuration = [
            'matomoWidgetsTitle' => '%env(SOME_TITLE)%',
            'matomoWidgetsUrl' => '%env(SOME_URL)%',
            'matomoWidgetsIdSite' => '%env(SOME_ID)%',
            'matomoWidgetsTokenAuth' => '%env(SOME_TOKEN)%',
            'matomoWidgetsActiveWidgets' => '%env(SOME_ACTIVE_WIDGETS)%',
            'matomoWidgetsPagesNotFoundTemplate' => '%env(SOME_ERROR_PAGES_TEMPLATE)%',
        ];
        $this->createSiteConfiguration('some_site', $configuration);

        $configurations = ConfigurationFinder::buildConfigurations(self::$configPath, true);
        $actual = $configurations->findConfigurationBySiteIdentifier('some_site');

        self::assertInstanceOf(Configuration::class, $actual);
        self::assertSame('The resolved title', $actual->siteTitle);
        self::assertSame('The resolved URL', $actual->url);
        self::assertSame(42, $actual->idSite);
        self::assertSame('The resolved token', $actual->tokenAuth);
        self::assertTrue($actual->isWidgetActive('widget1'));
        self::assertTrue($actual->isWidgetActive('widget2'));
        self::assertSame('The resolved pages not found template', $actual->pagesNotFoundTemplate);
    }

    #[Test]
    public function additionalConfigurationIsTakenIntoAccount(): void
    {
        $configuration = [
            'matomoWidgetsIdSite' => 42,
            'matomoWidgetsTitle' => 'Some Title',
            'matomoWidgetsTokenAuth' => 'some token',
            'matomoWidgetsUrl' => 'https://example.org/',
            'matomoWidgetsActiveWidgets' => 'actionsPerDay',
            'matomoWidgetsPagesNotFoundTemplate' => 'some 404 | {path} | {referrer}',
        ];
        $this->createAdditionalConfiguration('some_config.yaml', $configuration);
        $configurations = ConfigurationFinder::buildConfigurations(self::$configPath, false);

        self::assertCount(1, $configurations);

        /** @var Configuration $actualConfiguration */
        $actualConfiguration = $configurations->getIterator()->current();
        self::assertInstanceOf(Configuration::class, $actualConfiguration);
        self::assertSame('__some_config', $actualConfiguration->siteIdentifier);
        self::assertSame(42, $actualConfiguration->idSite);
        self::assertSame('Some Title', $actualConfiguration->siteTitle);
        self::assertSame('some token', $actualConfiguration->tokenAuth);
        self::assertSame('https://example.org/', $actualConfiguration->url);
        self::assertSame('some 404 | {path} | {referrer}', $actualConfiguration->pagesNotFoundTemplate);
        self::assertTrue($actualConfiguration->isWidgetActive('actionsPerDay'));
    }

    #[Test]
    public function siteConfigurationAndAdditionalConfigurationAreBothTakenIntoAccount(): void
    {
        $siteConfiguration = [
            'matomoWidgetsIdSite' => 41,
            'matomoWidgetsTitle' => 'Site Title',
            'matomoWidgetsTokenAuth' => 'site token',
            'matomoWidgetsUrl' => 'https://example.com/',
            'matomoWidgetsActiveWidgets' => 'visitsPerDay',
        ];
        $this->createSiteConfiguration('site_config', $siteConfiguration);

        $additionalConfiguration = [
            'matomoWidgetsIdSite' => 42,
            'matomoWidgetsTitle' => 'Additional Title',
            'matomoWidgetsTokenAuth' => 'additional token',
            'matomoWidgetsUrl' => 'https://example.org/',
            'matomoWidgetsActiveWidgets' => 'actionsPerDay',
        ];
        $this->createAdditionalConfiguration('additional_config.yaml', $additionalConfiguration);
        $configurations = ConfigurationFinder::buildConfigurations(self::$configPath, false);

        self::assertCount(2, $configurations);

        /** @var Configuration[] $configurationArray */
        $configurationArray = \iterator_to_array($configurations);

        self::assertInstanceOf(Configuration::class, $configurationArray[0]);
        self::assertSame('site_config', $configurationArray[0]->siteIdentifier);
        self::assertSame(41, $configurationArray[0]->idSite);
        self::assertSame('Site Title', $configurationArray[0]->siteTitle);
        self::assertSame('site token', $configurationArray[0]->tokenAuth);
        self::assertSame('https://example.com/', $configurationArray[0]->url);
        self::assertTrue($configurationArray[0]->isWidgetActive('visitsPerDay'));

        self::assertInstanceOf(Configuration::class, $configurationArray[1]);
        self::assertSame('__additional_config', $configurationArray[1]->siteIdentifier);
        self::assertSame(42, $configurationArray[1]->idSite);
        self::assertSame('Additional Title', $configurationArray[1]->siteTitle);
        self::assertSame('additional token', $configurationArray[1]->tokenAuth);
        self::assertSame('https://example.org/', $configurationArray[1]->url);
        self::assertTrue($configurationArray[1]->isWidgetActive('actionsPerDay'));
    }

    #[Test]
    public function siteConfigurationWithRelativeUrlFromMatomoIntegrationUsesSchemeFromBase(): void
    {
        $this->createSiteConfiguration('some_site', [
            'base' => 'https://example.com/',
            'matomoWidgetsConsiderMatomoIntegration' => true,
            'matomoIntegrationSiteId' => 1,
            'matomoIntegrationUrl' => '//matomo.example.com/',
        ]);

        $configurations = ConfigurationFinder::buildConfigurations(self::$configPath, true);
        $actualConfiguration = $configurations->getIterator()->current();

        self::assertSame('https://matomo.example.com/', $actualConfiguration->url);
    }

    private function createSiteConfiguration(string $identifier, array $configuration): void
    {
        $path = self::$configPath . '/sites/' . $identifier;
        \mkdir($path, 0777, true);
        \file_put_contents($path . '/config.yaml', $this->buildConfiguration($configuration));
    }

    private function buildConfiguration(array $configuration): string
    {
        $yamlConfiguration = '';
        foreach ($configuration as $key => $value) {
            if (\is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            $yamlConfiguration .= \sprintf('%s: "%s"', $key, $value) . \PHP_EOL;
        }

        return $yamlConfiguration;
    }

    private function createAdditionalConfiguration(string $filename, array $configuration): void
    {
        $path = self::$configPath . '/matomo_widgets';
        \mkdir($path, 0777, true);
        \file_put_contents($path . '/' . $filename, $this->buildConfiguration($configuration));
    }
}
