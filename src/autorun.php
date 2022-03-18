<?php

if (!mkdir(dirname(__DIR__ . '/Test')) && !is_dir(dirname(__DIR__ . '/Test'))) {
    throw new \RuntimeException(sprintf('Directory "%s" was not created', 'Test'));
}
