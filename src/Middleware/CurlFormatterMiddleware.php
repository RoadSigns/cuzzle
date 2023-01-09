<?php

declare(strict_types=1);

namespace RoadSigns\Cuzzle\Middleware;

use Closure;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use RoadSigns\Cuzzle\Formatter\CurlFormatter;

/**
 * Class CurlFormatterMiddleware middleware
 * it allows to attach the CurlFormatter to a Guzzle Request
 */
final class CurlFormatterMiddleware
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(callable $handler): Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $curlCommand = (new CurlFormatter())
                ->format($request, $options);
            $this->logger->debug((string) $curlCommand);

            return $handler($request, $options);
        };
    }
}
