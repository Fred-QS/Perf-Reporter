<?php

use Symfony\Component\HttpKernel\Bundle\Bundle;

class PerfsReportBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}