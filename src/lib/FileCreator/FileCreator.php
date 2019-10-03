<?php

namespace BuildInfo\FileCreator;

use BuildInfo\BuildInfo;

interface FileCreator {
    public function create(string $filename, BuildInfo $buildInfo): void;
}