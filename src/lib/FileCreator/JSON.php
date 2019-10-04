<?php

namespace Fostam\BuildInfo\FileCreator;

use Fostam\BuildInfo\BuildInfo;
use Exception;

class JSON implements FileCreator {
    /**
     * @param string $filename
     * @param BuildInfo $buildInfo
     * @throws Exception
     */
    public function create(string $filename, BuildInfo $buildInfo): void {
        $contents = json_encode($buildInfo->getRawData());

        if (!file_put_contents($filename, $contents)) {
            throw new Exception('cannot write file: ' . $filename);
        }
    }
}