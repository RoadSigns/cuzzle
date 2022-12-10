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
        $curl->addBody('{"foo":"bar"}');
        $this->assertEquals("curl 'http://example.local?foo=bar' -H 'foo: bar' -H 'Accept-Encoding: gzip,deflate,sdch' -d '{\"foo\":\"bar\"}'", (string) $curl);
    }

    public function testPOST()
    {
        $this->markTestIncomplete('TODO');
    }

    public function testPOSTWithHeader()
    {
        $this->markTestIncomplete('TODO');
    }

    public function testPOSTWithMultipleHeader()
    {
        $this->markTestIncomplete('TODO');
    }

    public function testPOSTWithMultipleHeaderAndBody()
    {
        $this->markTestIncomplete('TODO');
    }

    public function testPUT()
    {
        $this->markTestIncomplete('TODO');
    }

    public function testPUTWithHeader()
    {
        $this->markTestIncomplete('TODO');
    }

    public function testPUTWithMultipleHeader()
    {
        $this->markTestIncomplete('TODO');
    }

    public function testPUTWithMultipleHeaderAndBody()
    {
        $this->markTestIncomplete('TODO');
    }

    public function testDELETE()
    {
        $this->markTestIncomplete('TODO');
    }

    public function testDELETEWithHeader()
    {
        $this->markTestIncomplete('TODO');
    }

    public function testDELETEWithMultipleHeader()
    {
        $this->markTestIncomplete('TODO');
    }

    public function testDELETEWithMultipleHeaderAndBody()
    {
        $this->markTestIncomplete('TODO');
    }

    public function testHEAD()
    {
        $this->markTestIncomplete('TODO');
    }

    public function testHEADWithHeader()
    {
        $this->markTestIncomplete('TODO');
    }

    public function testHEADWithMultipleHeader()
    {
        $this->markTestIncomplete('TODO');
    }

    public function testAddOption()
    {
        $curl = new Curl();
        $curl->addOption('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], $curl->getOptions());
    }
}
