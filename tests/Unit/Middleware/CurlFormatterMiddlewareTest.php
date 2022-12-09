<?php

namespace RoadSigns\Cuzzle\Tests\Unit\Middleware;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RoadSigns\Cuzzle\Middleware\CurlFormatterMiddleware;

class CurlFormatterMiddlewareTest extends TestCase
{
    public function testGet()
    {
        $mock = new MockHandler([new Response(204)]);
        $handler = HandlerStack::create($mock);
        $logger = $this->createMock(LoggerInterface::class);

        $logger
            ->expects($this->once())
            ->method('debug')
            ->with("curl 'http://google.com' -A 'GuzzleHttp/7'");

        $handler->after('cookies', new CurlFormatterMiddleware($logger));
        $client = new Client(['handler' => $handler]);

        $client->get('http://google.com');
    }

    public function testPost()
    {
        $mock = new MockHandler([new Response(204)]);
        $handler = HandlerStack::create($mock);
        $logger = $this->createMock(LoggerInterface::class);

        $logger
            ->expects($this->once())
            ->method('debug')
            ->with("curl -X POST 'http://google.com' -A 'GuzzleHttp/7'");

        $handler->after('cookies', new CurlFormatterMiddleware($logger));
        $client = new Client(['handler' => $handler]);

        $client->post('http://google.com');
    }
}
