<?php

namespace BuildInfo\FileCreator;

use BuildInfo\BuildInfo;
use Exception;

class PHP implements FileCreator {
    /**
     * @param string $filename
     * @param BuildInfo $buildInfo
     * @throws \Exception
     */
    public function create(string $filename, BuildInfo $buildInfo): void {
        $buildInfo = $buildInfo->getRawData();

        $contents = "<?php\n\nreturn [\n";
        foreach($buildInfo as $key => $value) {
            if (is_null($value)) {
                $valueStr = 'null';
            }
            else {
                $valueStr = "'" . str_replace("'", "\'", (string) $value) . "'";
            }
            $contents .= "  '{$key}' => {$valueStr},\n";
        }
        $contents .= "];\n";

        if (!file_put_contents($filename, $contents)) {
            throw new Exception('cannot write file: ' . $filename);
        }
    }
}