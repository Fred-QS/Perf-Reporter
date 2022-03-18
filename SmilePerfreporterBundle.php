<?php

namespace Smile\PerfreporterBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SmilePerfreporterBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}