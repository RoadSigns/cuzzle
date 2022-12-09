<?php

namespace RoadSigns\Cuzzle\Formatter;

use Psr\Http\Message\RequestInterface;
use RoadSigns\Cuzzle\Command\CommandInterface;
use Stringable;

interface FormatterInterface
{
    public function format(RequestInterface $request, array $options = []): Stringable;
}
