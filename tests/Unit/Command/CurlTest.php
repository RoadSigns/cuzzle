<?php

declare(strict_types=1);

namespace RoadSigns\Cuzzle\Tests\Unit\Command;

use PHPUnit\Framework\TestCase;
use RoadSigns\Cuzzle\Command\Curl;

final class CurlTest extends TestCase
{
    public function testSimpleGET()
    {
        $curl = new Curl();
        $curl->addMethod('GET');
        $curl->addUrl('http://example.local');
        $this->assertEquals("curl 'http://example.local'", (string) $curl);
    }

    public function testSimpleGETWithHeader()
    {
        $curl = new Curl();
        $curl->addMethod('GET');
        $curl->addUrl('http://example.local');
        $curl->addHeader('foo', 'bar');
        $this->assertEquals("curl 'http://example.local' -H 'foo: bar'", (string) $curl);
    }

    public function testSimpleGETWithMultipleHeader()
    {
        $curl = new Curl();
        $curl->addMethod('GET');
        $curl->addUrl('http://example.local');
        $curl->addHeader('foo', 'bar');
        $curl->addHeader('Accept-Encoding', 'gzip,deflate,sdch');
        $this->assertEquals("curl 'http://example.local' -H 'foo: bar' -H 'Accept-Encoding: gzip,deflate,sdch'", (string) $curl);
    }

    public function testGETWithQueryString()
    {
        $curl = new Curl();
        $curl->addMethod('GET');
        $curl->addUrl('http://example.local?foo=bar');
        $this->assertEquals("curl 'http://example.local?foo=bar'", (string) $curl);
    }

    public function testGETWithQueryStringAndHeader()
    {
        $curl = new Curl();
        $curl->addMethod('GET');
        $curl->addUrl('http://example.local?foo=bar');
        $curl->addHeader('foo', 'bar');
        $this->assertEquals("curl 'http://example.local?foo=bar' -H 'foo: bar'", (string) $curl);
    }

    public function testGETWithQueryStringAndMultipleHeader()
    {
        $curl = new Curl();
        $curl->addMethod('GET');
        $curl->addUrl('http://example.local?foo=bar');
        $curl->addHeader('foo', 'bar');
        $curl->addHeader('Accept-Encoding', 'gzip,deflate,sdch');
        $this->assertEquals("curl 'http://example.local?foo=bar' -H 'foo: bar' -H 'Accept-Encoding: gzip,deflate,sdch'", (string) $curl);
    }

    public function testGETWithQueryStringAndMultipleHeaderAndBody()
    {
        $curl = new Curl();
        $curl->addMethod('GET');
        $curl->addUrl('http://example.local?foo=bar');
        $curl->addHeader('foo', 'bar');
        $curl->addHeader('Accept-Encoding', 'gzip,deflate,sdch');
        $curl->addOption('d', '{"foo":"bar"}');
        $this->assertEquals("curl 'http://example.local?foo=bar' -H 'foo: bar' -H 'Accept-Encoding: gzip,deflate,sdch' -d '{\"foo\":\"bar\"}'", (string) $curl);
    }

    public function testPOST()
    {
        $curl = new Curl();
        $curl->addMethod('POST');
        $curl->addUrl('http://example.local');
        $this->assertEquals("curl -X POST 'http://example.local'", (string) $curl);
    }

    public function testPOSTWithHeader()
    {
        $curl = new Curl();
        $curl->addMethod('POST');
        $curl->addUrl('http://example.local');
        $curl->addHeader('foo', 'bar');
        $this->assertEquals("curl -X POST 'http://example.local' -H 'foo: bar'", (string) $curl);
    }

    public function testPOSTWithMultipleHeader()
    {
        $curl = new Curl();
        $curl->addMethod('POST');
        $curl->addUrl('http://example.local');
        $curl->addHeader('foo', 'bar');
        $curl->addHeader('Accept-Encoding', 'gzip,deflate,sdch');
        $this->assertEquals("curl -X POST 'http://example.local' -H 'foo: bar' -H 'Accept-Encoding: gzip,deflate,sdch'", (string) $curl);
    }

    public function testPOSTWithMultipleHeaderAndBody()
    {
        $curl = new Curl();
        $curl->addMethod('POST');
        $curl->addUrl('http://example.local');
        $curl->addHeader('foo', 'bar');
        $curl->addHeader('Accept-Encoding', 'gzip,deflate,sdch');
        $curl->addOption('d', '{"foo":"bar"}');
        $this->assertEquals("curl -X POST 'http://example.local' -H 'foo: bar' -H 'Accept-Encoding: gzip,deflate,sdch' -d '{\"foo\":\"bar\"}'", (string) $curl);
    }

    public function testPUT()
    {
        $curl = new Curl();
        $curl->addMethod('PUT');
        $curl->addUrl('http://example.local');
        $this->assertEquals("curl -X PUT 'http://example.local'", (string) $curl);
    }

    public function testPUTWithHeader()
    {
        $curl = new Curl();
        $curl->addMethod('PUT');
        $curl->addUrl('http://example.local');
        $curl->addHeader('foo', 'bar');
        $this->assertEquals("curl -X PUT 'http://example.local' -H 'foo: bar'", (string) $curl);
    }

    public function testPUTWithMultipleHeader()
    {
        $curl = new Curl();
        $curl->addMethod('PUT');
        $curl->addUrl('http://example.local');
        $curl->addHeader('foo', 'bar');
        $curl->addHeader('Accept-Encoding', 'gzip,deflate,sdch');
        $this->assertEquals("curl -X PUT 'http://example.local' -H 'foo: bar' -H 'Accept-Encoding: gzip,deflate,sdch'", (string) $curl);
    }

    public function testDELETE()
    {
        $curl = new Curl();
        $curl->addMethod('DELETE');
        $curl->addUrl('http://example.local');
        $this->assertEquals("curl -X DELETE 'http://example.local'", (string) $curl);
    }

    public function testHEAD()
    {
        $curl = new Curl();
        $curl->addMethod('HEAD');
        $curl->addUrl('http://example.local');
        $this->assertEquals("curl --head 'http://example.local'", (string) $curl);
    }

    public function testMultipleOptionsWithSameName()
    {
        $curl = new Curl();
        $curl->addMethod('GET');
        $curl->addUrl('http://example.local');
        $curl->addOption('test', 'foo');
        $curl->addOption('test', 'bar');
        $this->assertEquals("curl 'http://example.local' --test 'foo' --test 'bar'", (string) $curl);
    }
}
