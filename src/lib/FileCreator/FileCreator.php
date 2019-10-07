<?php

namespace Fostam\BuildInfoCreate\FileCreator;

use Fostam\BuildInfo\BuildInfo;

interface FileCreator {
    public function create(string $filename, BuildInfo $buildInfo): void;
}