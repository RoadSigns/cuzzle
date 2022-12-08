<?php

namespace RoadSigns\Cuzzle\Formatter;

use Psr\Http\Message\RequestInterface;
use RoadSigns\Cuzzle\Command\CommandInterface;

interface FormatterInterface
{
    public function format(RequestInterface $request, array $options = []): CommandInterface;
}
