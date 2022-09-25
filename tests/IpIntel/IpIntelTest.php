<?php

declare(strict_types=1);

namespace Usox\IpIntel;

use Curl\Curl;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Usox\IpIntel\Exception\ServiceException;

class IpIntelTest extends MockeryTestCase
{
    /**
     * @var null|MockInterface|Curl
     */
    private $curl;

    /**
     * @var null|IpIntel
     */
    private $ipIntel;

    private $contactEmailAddress = 'foo@bar.baz';

    public function setUp(): void
    {
        $this->curl = Mockery::mock(Curl::class);

        $this->ipIntel = new IpIntel(
            $this->curl,
            $this->contactEmailAddress
        );
    }

    public function testValidateReturnsFalseIfAbovePropability(): void
    {
        $ip = '666.42.33.21';

        $this->curl->shouldReceive('setTimeout')
            ->with(5)
            ->once();
        $this->curl->shouldReceive('get')
            ->with(
                'https://check.getipintel.net/check.php',
                [
                    'ip' => $ip,
                    'contact' => $this->contactEmailAddress
                ]
            )
            ->once()
            ->andReturn('888');

        $this->assertFalse(
            $this->ipIntel->validate($ip, 777)
        );
    }

    public function testValidateAppendsCustomFlagAndReturnsTrueIfBelowPropability(): void
    {
        $ipIntel = new IpIntel(
            $this->curl,
            $this->contactEmailAddress,
            'm'
        );
        $ip = '666.42.33.21';

        $this->curl->shouldReceive('setTimeout')
            ->with(5)
            ->once();
        $this->curl->shouldReceive('get')
            ->with(
                'https://check.getipintel.net/check.php',
                [
                    'ip' => $ip,
                    'contact' => $this->contactEmailAddress,
                    'flags' => 'm'
                ]
            )
            ->once()
            ->andReturn('0.1');

        $this->assertTrue(
            $ipIntel->validate($ip, 0.2)
        );
    }

    public function testValidateThrowsExceptionOnServiceError(): void
    {
        $this->expectException(ServiceException::class);

        $ip = '666.42.33.21';

        $this->curl->shouldReceive('setTimeout')
            ->with(5)
            ->once();
        $this->curl->shouldReceive('get')
            ->with(
                'https://check.getipintel.net/check.php',
                [
                    'ip' => $ip,
                    'contact' => $this->contactEmailAddress
                ]
            )
            ->once()
            ->andReturn('');

        $this->ipIntel->validate($ip, 0.2);
    }

    public function testValidateThrowsExceptionOnEmptyResponse(): void
    {
        $this->expectException(ServiceException::class);

        $ip = '666.42.33.21';

        $this->curl->shouldReceive('setTimeout')
            ->with(5)
            ->once();

        $this->curl->shouldReceive('get')
            ->with(
                'https://check.getipintel.net/check.php',
                [
                    'ip' => $ip,
                    'contact' => $this->contactEmailAddress
                ]
            )
            ->once()
            ->andReturnNull();

        $this->ipIntel->validate($ip, 0.2);
    }
}
