<?php

declare(strict_types=1);

namespace RoadSigns\Cuzzle\Formatter;

use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Cookie\SetCookie;
use Psr\Http\Message\RequestInterface;
use RoadSigns\Cuzzle\Command\Curl;
use Stringable;

/**
 * Class CurlFormatter it formats a Guzzle request to a cURL shell command
 */
final class CurlFormatter implements FormatterInterface
{
    /**
     * @param RequestInterface $request
     * @param array<string, mixed> $options
     * @return Curl
     */
    public function format(RequestInterface $request, array $options = []): Stringable
    {
        $curl = new Curl();

        $this->extractHttpMethodArgument($request, $curl);
        $this->extractUrl($request, $curl);
        $this->extractHeadersArgument($request, $curl);
        $this->extractCookiesArgument($request, $options, $curl);
        $this->extractBodyArgument($request, $curl);

        return $curl;
    }

    private function extractHttpMethodArgument(RequestInterface $request, Curl $curl): void
    {
        $curl->addMethod($request->getMethod());
    }

    private function extractBodyArgument(RequestInterface $request, Curl $curl): void
    {
        $previousPosition = 0;
        $body = $request->getBody();

        if ($body->isSeekable()) {
            $previousPosition = $body->tell();
            $body->rewind();
        }

        $contents = $body->getContents();

        if ($body->isSeekable()) {
            $body->seek($previousPosition);
        }

        if ($contents) {
            //if get request has data Add G otherwise curl will make a post request
            if ('GET' === $request->getMethod()) {
                $curl->addOption('G');
            }

            // clean input of null bytes
            $contents = str_replace(chr(0), '', $contents);
            $curl->addOption('d', escapeshellarg($contents));
        }
    }

    /**
     * @param RequestInterface $request
     * @param array<string, mixed> $options
     * @param Curl $curl
     * @return void
     */
    private function extractCookiesArgument(RequestInterface $request, array $options, Curl $curl): void
    {
        if (!isset($options['cookies']) || !$options['cookies'] instanceof CookieJarInterface) {
            return;
        }

        $values = [];
        $scheme = $request->getUri()->getScheme();
        $host = $request->getUri()->getHost();
        $path = $request->getUri()->getPath();

        /** @var SetCookie $cookie */
        foreach ($options['cookies'] as $cookie) {
            if ($cookie->matchesPath($path) && $cookie->matchesDomain($host) &&
                !$cookie->isExpired() && (!$cookie->getSecure() || $scheme === 'https')) {
                $values[] = $cookie->getName() . '=' . $cookie->getValue();
            }
        }

        if ($values) {
            $curl->addOption('b', escapeshellarg(implode('; ', $values)));
        }
    }

    private function extractHeadersArgument(RequestInterface $request, Curl $curl): void
    {
        foreach ($request->getHeaders() as $name => $header) {
            if ('host' === strtolower($name) && $header[0] === $request->getUri()->getHost()) {
                continue;
            }

            if ('user-agent' === strtolower($name)) {
                $curl->addOption('A', escapeshellarg($header[0]));
                continue;
            }

            foreach ($header as $headerValue) {
                $curl->addHeader($name, $headerValue);
            }
        }
    }

    private function extractUrl(RequestInterface $request, Curl $curl): void
    {
        $curl->addUrl((string) $request->getUri()->withFragment(''));
    }
}
