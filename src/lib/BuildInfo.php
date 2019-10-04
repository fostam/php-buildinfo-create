<?php

namespace Fostam\BuildInfo;

use DateTime;
use Exception;

class BuildInfo {
    const NAME = 'name';
    const TIME = 'time';
    const VERSION = 'version';
    const BUILD_NUMBER = 'buildNumber';
    const BRANCH = 'branch';
    const COMMIT = 'commit';

    /** @var array */
    private $buildInfo = [
        self::NAME => null,
        self::TIME => null,
        self::VERSION => null,
        self::BUILD_NUMBER => null,
        self::BRANCH => null,
        self::COMMIT => null,
    ];

    /**
     * BuildInfo constructor.
     */
    protected function __construct() {
    }

    /**
     * @param string $buildFileName
     * @return BuildInfo
     * @throws Exception
     */
    public static function fromFile(string $buildFileName): BuildInfo {
        $buildInfo = self::load($buildFileName);
        return self::fromArray($buildInfo);
    }

    /**
     * @param array $buildInfoData
     * @return BuildInfo
     */
    public static function fromArray(array $buildInfoData): BuildInfo {
        $buildInfo = new BuildInfo();
        $buildInfo->setByArray($buildInfoData);
        return $buildInfo;
    }

    /**
     * @param array $buildInfoData
     */
    protected function setByArray(array $buildInfoData): void {
        foreach($buildInfoData as $key => $value) {
            if (array_key_exists($key, $this->buildInfo) || preg_match('#^x-#', $key)) {
                $this->buildInfo[$key] = $value;
            }
        }
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function getName(): ?string {
        return $this->buildInfo[self::NAME] ?? null;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function getTime(): ?string {
        return $this->buildInfo[self::TIME] ?? null;
    }

    /**
     * @return DateTime|null
     * @throws Exception
     */
    public function getTimeAsDateTime(): ?DateTime {
        if (empty($this->buildInfo[self::TIME])) {
            return null;
        }

        try {
            $dt = new DateTime($this->buildInfo[self::TIME]);
        }
        catch (Exception $e) {
            return null;
        }

        return $dt;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function getVersion(): ?string {
        return $this->buildInfo[self::VERSION] ?? null;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function getBuildNumber(): ?string {
        return $this->buildInfo[self::BUILD_NUMBER] ?? null;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function getBranch(): ?string {
        return $this->buildInfo[self::BRANCH] ?? null;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function getCommit(): ?string {
        return $this->buildInfo[self::COMMIT] ?? null;
    }

    /**
     * @param $customParam
     * @return string|null
     */
    public function get($customParam): ?string {
        return $this->buildInfo['x-' . $customParam] ?? null;
    }

    /**
     * @param bool $withNullValues
     * @return array
     */
    public function getRawData(bool $withNullValues = false): array {
        if ($withNullValues) {
            return $this->buildInfo;
        }

        $buildInfo = [];
        foreach($this->buildInfo as $key => $value) {
            if (!is_null($value)) {
                $buildInfo[$key] = $value;
            }
        }

        return $buildInfo;
    }

    /**
     * @param string $buildFileName
     * @return array
     * @throws Exception
     */
    private static function load(string $buildFileName): array {
        if (!file_exists($buildFileName)) {
            throw new Exception('file does not exist: ' . $buildFileName);
        }

        $pathinfo = pathinfo($buildFileName);
        $extension = $pathinfo['extension'] ?? '';

        if ($extension === 'php') {
            /** @noinspection PhpIncludeInspection */
            $buildInfo = include($buildFileName);
        }
        else if ($extension === 'json') {
            $jsonData = file_get_contents($buildFileName);
            $buildInfo = json_decode($jsonData, true);
        }
        else {
            throw new Exception('unsupported file type: ' . $extension);
        }

        return $buildInfo;
    }
}