<?php

namespace unit\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Psr7\Request;
use RoadSigns\Cuzzle\Formatter\CurlFormatter;

class RequestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CurlFormatter
     */
    protected $curlFormatter;

    public function setUp(): void
    {
        $this->client        = new Client();
        $this->curlFormatter = new CurlFormatter();
    }

    public function testGetWithCookies()
    {
        $request = new Request('GET', 'http://local.example');
        $jar = CookieJar::fromArray(['Foo' => 'Bar', 'identity' => 'xyz'], 'local.example');
        $curl = $this->curlFormatter->format($request, ['cookies' => $jar]);

        var_dump($curl);
        die();

        $this->assertStringNotContainsString("-H 'Host: local.example'", $curl);
        $this->assertStringContainsString("-b 'Foo=Bar; identity=xyz'", $curl);
    }

    public function testPOST()
    {
        $request = new Request('POST', 'http://local.example', [], 'foo=bar&hello=world');
        $curl    = $this->curlFormatter->format($request);

        $this->assertStringContainsString("-d 'foo=bar&hello=world'", $curl);
    }

    public function testPUT()
    {
        $request = new Request('PUT', 'http://local.example', [], 'foo=bar&hello=world');
        $curl    = $this->curlFormatter->format($request);

        $this->assertStringContainsString("-d 'foo=bar&hello=world'", $curl);
        $this->assertStringContainsString('-X PUT', $curl);
    }

    public function testDELETE()
    {
        $request = new Request('DELETE', 'http://local.example');
        $curl    = $this->curlFormatter->format($request);

        $this->assertStringContainsString('-X DELETE', $curl);
    }

    public function testHEAD()
    {
        $request = new Request('HEAD', 'http://local.example');
        $curl    = $this->curlFormatter->format($request);

        $this->assertStringContainsString("curl 'http://local.example' --head", $curl);
    }

    public function testOPTIONS()
    {
        $request = new Request('OPTIONS', 'http://local.example');
        $curl    = $this->curlFormatter->format($request);

        $this->assertStringContainsString('-X OPTIONS', $curl);
    }
}