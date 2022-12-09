<?php

declare(strict_types=1);

namespace RoadSigns\Cuzzle\Tests\Unit\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use RoadSigns\Cuzzle\Formatter\CurlFormatter;

final class RequestTest extends TestCase
{
    private CurlFormatter $curlFormatter;

    public function setUp(): void
    {
        $this->client = new Client();
        $this->curlFormatter = new CurlFormatter();
    }

    public function testGetWithCookies(): void
    {
        $request = new Request('GET', 'http://local.example');
        $jar = CookieJar::fromArray(['Foo' => 'Bar', 'identity' => 'xyz'], 'local.example');
        $curl = $this->curlFormatter->format($request, ['cookies' => $jar]);

        $this->assertStringNotContainsString("-H 'Host: local.example'", $curl);
        $this->assertStringContainsString("-b 'Foo=Bar; identity=xyz'", $curl);
    }

    public function testPOST(): void
    {
        $request = new Request('POST', 'http://local.example', [], 'foo=bar&hello=world');
        $curl = $this->curlFormatter->format($request)->toString();

        $this->assertStringContainsString("-d 'foo=bar&hello=world'", $curl);
    }

    public function testPUT()
    {
        $request = new Request('PUT', 'http://local.example', [], 'foo=bar&hello=world');
        $curl = $this->curlFormatter->format($request)->toString();

        $this->assertStringContainsString("-d 'foo=bar&hello=world'", $curl);
        $this->assertStringContainsString('-X PUT', $curl);
    }

    public function testDELETE()
    {
        $request = new Request('DELETE', 'http://local.example');
        $curl = $this->curlFormatter->format($request)->toString();

        $this->assertSame("curl -X DELETE 'http://local.example'", $curl);
    }

    public function testHEAD()
    {
        $request = new Request('HEAD', 'http://local.example');
        $curl = $this->curlFormatter->format($request)->toString();

        $this->assertSame("curl --head 'http://local.example'", $curl);
    }

    public function testOPTIONS()
    {
        $request = new Request('OPTIONS', 'http://local.example');
        $curl = $this->curlFormatter->format($request)->toString();

        $this->assertStringContainsString('-X OPTIONS', $curl);
    }
}
