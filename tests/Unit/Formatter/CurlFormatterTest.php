<?php

declare(strict_types=1);

namespace RoadSigns\Cuzzle\Tests\Unit\Formatter;

use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use RoadSigns\Cuzzle\Formatter\CurlFormatter;

final class CurlFormatterTest extends TestCase
{
    private CurlFormatter $curlFormatter;

    public function setUp(): void
    {
        $this->curlFormatter = new CurlFormatter();
    }

    public function testMultiLineDisabled()
    {
        $this->curlFormatter->setCommandLineLength(10);

        $request = new Request('GET', 'http://example.local', ['foo' => 'bar']);
        $curl = $this->curlFormatter->format($request)->toString();

        $this->assertEquals(substr_count($curl, "\n"), 2);
    }

    public function testSkipHostInHeaders()
    {
        $request = new Request('GET', 'http://example.local');
        $curl = $this->curlFormatter->format($request)->toString();

        $this->assertEquals("curl 'http://example.local'", $curl);
    }

    public function testSimpleGET()
    {
        $request = new Request('GET', 'http://example.local');
        $curl = $this->curlFormatter->format($request);

        $this->assertEquals("curl 'http://example.local'", $curl->toString());
    }

    public function testSimpleGETWithHeader()
    {
        $request = new Request('GET', 'http://example.local', [
            'foo' => 'bar'
        ]);
        $curl = $this->curlFormatter->format($request);

        $this->assertEquals("curl 'http://example.local' -H 'foo: bar'", $curl->toString());    }

    public function testSimpleGETWithMultipleHeader()
    {
        $request = new Request('GET', 'http://example.local', [
            'foo' => 'bar',
            'Accept-Encoding' => 'gzip,deflate,sdch'
        ]);
        $curl = $this->curlFormatter->format($request);

        $this->assertEquals("curl 'http://example.local' -H 'foo: bar' -H 'Accept-Encoding: gzip,deflate,sdch'", $curl);
    }

    public function testGETWithQueryString()
    {
        $request = new Request('GET', 'http://example.local?foo=bar');
        $curl = $this->curlFormatter->format($request);

        $this->assertEquals("curl 'http://example.local?foo=bar'", $curl);

        $request = new Request('GET', 'http://example.local?foo=bar');
        $curl = $this->curlFormatter->format($request);

        $this->assertEquals("curl 'http://example.local?foo=bar'", $curl);

        $body = \GuzzleHttp\Psr7\stream_for(http_build_query(['foo' => 'bar', 'hello' => 'world'], '', '&'));

        $request = new Request('GET', 'http://example.local', [], $body);
        $curl = $this->curlFormatter->format($request);

        $this->assertEquals("curl 'http://example.local' -G  -d 'foo=bar&hello=world'", $curl);
    }

    public function testPOST()
    {
        $body = http_build_query(['foo' => 'bar', 'hello' => 'world'], '', '&');

        $request = new Request('POST', 'http://example.local', [], $body);
        $curl = $this->curlFormatter->format($request);

        $this->assertStringContainsString("-d 'foo=bar&hello=world'", $curl);
        $this->assertStringNotContainsString(" -G ", $curl);
    }

    public function testHEAD()
    {
        $request = new Request('HEAD', 'http://example.local');
        $curl = $this->curlFormatter->format($request);

        $this->assertStringContainsString("--head", $curl);
    }

    public function testOPTIONS()
    {
        $request = new Request('OPTIONS', 'http://example.local');
        $curl = $this->curlFormatter->format($request);

        $this->assertStringContainsString("-X OPTIONS", $curl);
    }

    public function testDELETE()
    {
        $request = new Request('DELETE', 'http://example.local/users/4');
        $curl = $this->curlFormatter->format($request);

        $this->assertStringContainsString("-X DELETE", $curl);
    }

    public function testPUT()
    {
        $request = new Request('PUT', 'http://example.local', [], 'foo=bar&hello=world');
        $curl = $this->curlFormatter->format($request);

        $this->assertStringContainsString("-d 'foo=bar&hello=world'", $curl);
        $this->assertStringContainsString("-X PUT", $curl);
    }

    public function testProperBodyReading()
    {
        $request = new Request('PUT', 'http://example.local', [], 'foo=bar&hello=world');
        $request->getBody()->getContents();

        $curl = $this->curlFormatter->format($request);

        $this->assertStringContainsString("-d 'foo=bar&hello=world'", $curl);
        $this->assertStringContainsString("-X PUT", $curl);
    }

    /**
     * @dataProvider getHeadersAndBodyData
     */
    public function testExtractBodyArgument($headers, $body)
    {
        // clean input of null bytes
        $body = str_replace(chr(0), '', $body);
        $request = new Request('POST', 'http://example.local', $headers, $body);

        $curl = $this->curlFormatter->format($request);

        $this->assertStringContainsString('foo=bar&hello=world', $curl);
    }

    /**
     * The data provider for testExtractBodyArgument
     *
     * @return array
     */
    public function getHeadersAndBodyData()
    {
        return [
            [
                ['X-Foo' => 'Bar'],
                chr(0) . 'foo=bar&hello=world',
            ],
        ];
    }
}