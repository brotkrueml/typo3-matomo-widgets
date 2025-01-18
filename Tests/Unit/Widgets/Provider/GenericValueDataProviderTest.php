<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_widgets" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoWidgets\Tests\Unit\Widgets\Provider;

use Brotkrueml\MatomoWidgets\Connection\ConnectionConfiguration;
use Brotkrueml\MatomoWidgets\Domain\Repository\MatomoRepository;
use Brotkrueml\MatomoWidgets\Parameter\ParameterBag;
use Brotkrueml\MatomoWidgets\Widgets\Provider\GenericValueDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(GenericValueDataProvider::class)]
final class GenericValueDataProviderTest extends TestCase
{
    private ConnectionConfiguration $connectionConfiguration;
    private MatomoRepository&Stub $repositoryStub;
    private GenericValueDataProvider $subject;

    protected function setUp(): void
    {
        $this->connectionConfiguration = new ConnectionConfiguration('https://example.org/', 1, '');
        $this->repositoryStub = self::createStub(MatomoRepository::class);
        $this->subject = new GenericValueDataProvider(
            $this->repositoryStub,
            $this->connectionConfiguration,
            'some.method',
            'the_column',
            [
                'foo' => 'bar',
            ],
        );
    }

    #[Test]
    public function getValueForExistingColumn(): void
    {
        $this->repositoryStub
            ->method('send')
            ->with($this->connectionConfiguration, 'some.method', new ParameterBag([
                'foo' => 'bar',
            ]))
            ->willReturn([
                'the_column' => '123',
                'another_column' => 987,
            ]);

        $actual = $this->subject->getValue();

        self::assertSame('123', $actual);
    }

    #[Test]
    public function getValueForNonExistingColumn(): void
    {
        $this->repositoryStub
            ->method('send')
            ->with($this->connectionConfiguration, 'some.method', new ParameterBag([
                'foo' => 'bar',
            ]))
            ->willReturn([
                'a_column' => 987,
            ]);

        $actual = $this->subject->getValue();

        self::assertSame('', $actual);
    }
}
