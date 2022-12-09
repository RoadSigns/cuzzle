<?php

declare(strict_types=1);

namespace RoadSigns\Cuzzle\Command;

use Stringable;

final class Curl implements Stringable
{
    private string $command;

    private array $options;

    private string $method;

    private string $url;

    public function __construct()
    {
        $this->command = 'curl';
        $this->method = 'GET';
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
        var_dump($url);
        $this->url = $url;
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

    public function __toString(): string
    {
        $command = $this->command;
        $command = $this->addMethodToCommand($command);
        $command = $this->addUrlToCommand($command);
        $command = $this->addOptionsToCommand($command);
        return $command;
    }
}
