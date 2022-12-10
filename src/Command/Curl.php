<?php

declare(strict_types=1);

namespace RoadSigns\Cuzzle\Command;

use Stringable;

final class Curl implements Stringable
{
    private string $command;

    /** @var array<string, mixed> */
    private array $options;

    /** @var array<string, mixed> */
    private array $headers;

    private string $method;

    private string $url;

    public function __construct()
    {
        $this->command = 'curl';
        $this->method = 'GET';
        $this->headers = [];
        $this->options = [];
    }

    public function addOption(string $name, mixed $value = null): void
    {
        if (isset($this->options[$name])) {
            if (!is_array($this->options[$name])) {
                $this->options[$name] = (array)$this->options[$name];
            }

            $this->options[$name][] = $value;
        } else {
            $this->options[$name] = $value;
        }
    }

    public function addUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function addHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function addMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    private function addOptionsToCommand(string $command): string
    {
        $options = $this->options;
        ksort($options);

        if (count($this->options)) {
            foreach ($this->options as $name => $value) {
                if (is_array($value)) {
                    foreach ($value as $subValue) {
                        $command = $this->addCommandPart($command, "-{$name} {$subValue}");
                    }
                } else {
                    $command = $this->addCommandPart($command, "-{$name} {$value}");
                }
            }
        }

        return $command;
    }


    private function addCommandPart(string $command, string $part): string
    {
        return $command . ' ' . $part;
    }

    private function addUrlToCommand(string $command): string
    {
        return $command . " " . escapeshellarg($this->url);
    }

    private function addMethodToCommand(string $command): string
    {
        if ('GET' === $this->method) {
            return $command;
        }

        $command .= 'HEAD' === $this->method
            ? ' --head'
            : ' -X ' . $this->method;

        return $command;
    }

    private function addHeadersToCommand(string $command): string
    {
        if (count($this->headers)) {
            foreach ($this->headers as $name => $value) {
                $header = escapeshellarg("{$name}: {$value}");
                $command = $this->addCommandPart($command, "-H $header");
            }
        }

        return $command;
    }

    public function __toString(): string
    {
        $command = $this->command;
        $command = $this->addMethodToCommand($command);
        $command = $this->addUrlToCommand($command);
        $command = $this->addHeadersToCommand($command);
        $command = $this->addOptionsToCommand($command);
        return $command;
    }
}
