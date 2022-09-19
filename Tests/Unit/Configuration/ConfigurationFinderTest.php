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
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Core\ApplicationContext;
use TYPO3\CMS\Core\Core\Environment;

/**
 * @runTestsInSeparateProcesses
 * @covers \Brotkrueml\MatomoWidgets\Configuration\ConfigurationFinder
 */
final class ConfigurationFinderTest extends TestCase
{
    private static string $configPath;

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
            ''
        );

        self::$configPath = \realpath(\sys_get_temp_dir()) . \DIRECTORY_SEPARATOR . 'matomo_widgets_configuration_finder';

        if (\is_dir(self::$configPath)) {
            self::tearDownAfterClass();
        } else {
            \mkdir(self::$configPath);
        }
    }

    public static function tearDownAfterClass(): void
    {
        $paths = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(self::$configPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
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
        self::tearDownAfterClass();
    }

    /**
     * @test
     */
    public function noSiteConfigurationFoundThenNoMatomoConfigurationExists(): void
    {
        $configurations = ConfigurationFinder::buildConfigurations(self::$configPath, false);

        self::assertCount(0, $configurations);
    }

    /**
     * @test
     */
    public function siteConfigurationWithNoMatomoConfigurationIsNotTakenIntoAccount(): void
    {
        $this->createSiteConfiguration('some_site', [
            'rootPageId' => 1,
        ]);
        $configurations = ConfigurationFinder::buildConfigurations(self::$configPath, false);

        self::assertCount(0, $configurations);
    }

    /**
     * @test
     */
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

        self::assertCount(0, $configurations);
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
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

        self::assertCount(0, $configurations);
    }

    /**
     * @test
     */
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

        self::assertCount(0, $configurations);
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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

        self::assertCount(0, $configurations);
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
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

        self::assertSame('The resolved title', $actual->siteTitle);
        self::assertSame('The resolved URL', $actual->url);
        self::assertSame(42, $actual->idSite);
        self::assertSame('The resolved token', $actual->tokenAuth);
        self::assertTrue($actual->isWidgetActive('widget1'));
        self::assertTrue($actual->isWidgetActive('widget2'));
        self::assertSame('The resolved pages not found template', $actual->pagesNotFoundTemplate);
    }

    private function createSiteConfiguration(string $identifier, array $configuration): void
    {
        $path = self::$configPath . '/sites/' . $identifier;
        \mkdir($path, 0777, true);

        $yamlConfiguration = '';
        foreach ($configuration as $key => $value) {
            if (\is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            $yamlConfiguration .= \sprintf('%s: "%s"', $key, $value) . PHP_EOL;
        }
        \file_put_contents($path . '/config.yaml', $yamlConfiguration);
    }
}
