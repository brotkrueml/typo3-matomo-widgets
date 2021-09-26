<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Configuration;

use Brotkrueml\MatomoWidgets\Adapter\ExtensionAvailability;
use Brotkrueml\MatomoWidgets\Configuration\Configuration;
use Brotkrueml\MatomoWidgets\Configuration\ConfigurationFinder;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Core\ApplicationContext;
use TYPO3\CMS\Core\Core\Environment;

/**
 * @runInSeparateProcess
 */
class ConfigurationFinderTest extends TestCase
{
    /** @var ExtensionAvailability|Stub */
    private $extensionAvailabilityStub;

    /** @var YamlFileLoader */
    private $yamlFileLoader;

    /** @var string */
    private static $configPath;

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

    protected function setUp(): void
    {
        $this->yamlFileLoader = new YamlFileLoader();
        $this->yamlFileLoader->setLogger(new NullLogger());
    }

    protected function tearDown(): void
    {
        self::tearDownAfterClass();
    }

    /**
     * @test
     */
    public function classIsTraversable(): void
    {
        $subject = new ConfigurationFinder(self::$configPath, false, $this->yamlFileLoader);

        self::assertInstanceOf(\Traversable::class, $subject);
    }

    /**
     * @test
     */
    public function classImplementsCountable(): void
    {
        $subject = new ConfigurationFinder(self::$configPath, false, $this->yamlFileLoader);

        self::assertInstanceOf(\Countable::class, $subject);
    }

    /**
     * @test
     */
    public function noSiteConfigurationFoundThenNoMatomoConfigurationExists(): void
    {
        $subject = new ConfigurationFinder(self::$configPath, false, $this->yamlFileLoader);

        self::assertCount(0, $subject);
    }

    /**
     * @test
     */
    public function siteConfigurationWithNoMatomoConfigurationIsNotTakenIntoAccount(): void
    {
        $this->createSiteConfiguration('some_site', ['rootPageId' => 1]);
        $subject = new ConfigurationFinder(self::$configPath, false, $this->yamlFileLoader);

        self::assertCount(0, $subject);
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
        $subject = new ConfigurationFinder(self::$configPath, false, $this->yamlFileLoader);

        self::assertCount(0, $subject);
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
        ];
        $this->createSiteConfiguration('some_site', $configuration);
        $subject = new ConfigurationFinder(self::$configPath, false, $this->yamlFileLoader);

        self::assertCount(1, $subject);

        /** @var Configuration $actualConfiguration */
        $actualConfiguration = $subject->getIterator()->current();
        self::assertInstanceOf(Configuration::class, $actualConfiguration);
        self::assertSame('some_site', $actualConfiguration->getSiteIdentifier());
        self::assertSame(42, $actualConfiguration->getIdSite());
        self::assertSame('Some Title', $actualConfiguration->getSiteTitle());
        self::assertSame('some token', $actualConfiguration->getTokenAuth());
        self::assertSame('https://example.org/', $actualConfiguration->getUrl());
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
        $subject = new ConfigurationFinder(self::$configPath, false, $this->yamlFileLoader);

        self::assertCount(0, $subject);
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
        $subject = new ConfigurationFinder(self::$configPath, false, $this->yamlFileLoader);

        self::assertCount(0, $subject);
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
            'matomoWidgetsConsiderMatomoIntegration' => true,
            'matomoIntegrationUrl' => 'https://example.com/',
            'matomoIntegrationSiteId' => 1,
        ];
        $this->createSiteConfiguration('some_site', $configuration);
        $subject = new ConfigurationFinder(self::$configPath, false, $this->yamlFileLoader);

        self::assertCount(1, $subject);

        /** @var Configuration $actualConfiguration */
        $actualConfiguration = $subject->getIterator()->current();
        self::assertSame(42, $actualConfiguration->getIdSite());
        self::assertSame('https://example.org/', $actualConfiguration->getUrl());
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
            'matomoWidgetsConsiderMatomoIntegration' => true,
            'matomoIntegrationUrl' => 'https://example.com/',
            'matomoIntegrationSiteId' => 1,
        ];
        $this->createSiteConfiguration('some_site', $configuration);
        $subject = new ConfigurationFinder(self::$configPath, true, $this->yamlFileLoader);

        self::assertCount(1, $subject);

        /** @var Configuration $actualConfiguration */
        $actualConfiguration = $subject->getIterator()->current();
        self::assertSame(1, $actualConfiguration->getIdSite());
        self::assertSame('https://example.com/', $actualConfiguration->getUrl());
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
        $subject = new ConfigurationFinder(self::$configPath, true, $this->yamlFileLoader);

        self::assertCount(0, $subject);
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

            $yamlConfiguration .= \sprintf('%s: %s', $key, $value) . PHP_EOL;
        }
        \file_put_contents($path . '/config.yaml', $yamlConfiguration);
    }
}
