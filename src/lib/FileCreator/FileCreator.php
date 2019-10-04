<?php

namespace Fostam\BuildInfo\FileCreator;

use Fostam\BuildInfo\BuildInfo;

interface FileCreator {
    public function create(string $filename, BuildInfo $buildInfo): void;
}