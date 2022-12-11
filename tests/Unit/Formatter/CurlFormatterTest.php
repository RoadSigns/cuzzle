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

    public function testSkipHostInHeaders()
    {
        $request = new Request('GET', 'http://example.local');
        $curl = $this->curlFormatter->format($request);

        $this->assertEquals("curl 'http://example.local'", $curl);
    }

    public function testSimpleGET()
    {
        $request = new Request('GET', 'http://example.local');
        $curl = $this->curlFormatter->format($request);

        $this->assertEquals("curl 'http://example.local'", $curl);
    }

    public function testSimpleGETWithHeader()
    {
        $request = new Request('GET', 'http://example.local', [
            'foo' => 'bar'
        ]);
        $curl = $this->curlFormatter->format($request);

        $this->assertEquals("curl 'http://example.local' -H 'foo: bar'", $curl);
    }

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
    }

    public function testGETWithBody()
    {
        $body = ['foo' => 'bar', 'hello' => 'world'];

        $request = new Request('GET', 'http://example.local', [], json_encode($body));
        $curl = $this->curlFormatter->format($request);

        $this->assertEquals("curl 'http://example.local' -G -d '{\"foo\":\"bar\",\"hello\":\"world\"}'", (string) $curl);
    }

    public function testPOST()
    {
        $body = http_build_query(['foo' => 'bar', 'hello' => 'world'], '', '&');

        $request = new Request('POST', 'http://example.local', [], $body);
        $curl = $this->curlFormatter->format($request);

        $this->assertStringContainsString("-d 'foo=bar&hello=world'", (string) $curl);
        $this->assertStringNotContainsString(" -G ", (string) $curl);
    }

    public function testHEAD()
    {
        $request = new Request('HEAD', 'http://example.local');
        $curl = $this->curlFormatter->format($request);

        $this->assertSame("curl --head 'http://example.local'", (string) $curl);
    }

    public function testOPTIONS()
    {
        $request = new Request('OPTIONS', 'http://example.local');
        $curl = $this->curlFormatter->format($request);

        $this->assertStringContainsString("-X OPTIONS", (string) $curl);
    }

    public function testDELETE()
    {
        $request = new Request('DELETE', 'http://example.local/users/4');
        $curl = $this->curlFormatter->format($request);

        $this->assertSame("curl -X DELETE 'http://example.local/users/4'", (string) $curl);
    }

    public function testPUT()
    {
        $request = new Request('PUT', 'http://example.local', [], 'foo=bar&hello=world');
        $curl = $this->curlFormatter->format($request);

        $this->assertSame("curl -X PUT 'http://example.local' -d 'foo=bar&hello=world'", (string) $curl);
    }

    public function testProperBodyReading()
    {
        $request = new Request('PUT', 'http://example.local', [], 'foo=bar&hello=world');
        $request->getBody()->getContents();

        $curl = $this->curlFormatter->format($request);

        $this->assertStringContainsString("-d 'foo=bar&hello=world'", (string)$curl);
        $this->assertStringContainsString("-X PUT", (string)$curl);
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

        $this->assertStringContainsString('foo=bar&hello=world', (string)$curl);
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
