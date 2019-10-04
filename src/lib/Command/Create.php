<?php

namespace Fostam\BuildInfo\Command;

use DateTime;
use DateTimeZone;
use Exception;
use Fostam\BuildInfo\BuildInfo;
use Fostam\BuildInfo\FileCreator\FileCreator;
use Fostam\BuildInfo\FileCreator\JSON;
use Fostam\BuildInfo\FileCreator\PHP;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command {
    private $supportedFiletypes = [
        'json' => JSON::class,
        'php' => PHP::class,
    ];

    /**
     *
     */
    protected function configure() {
        $this->setName('create');
        $this->setDescription('create info file(s)');
        $this->setDefinition(
            new InputDefinition([
                                    new InputArgument('target', InputArgument::REQUIRED | InputArgument::IS_ARRAY),
                                    new InputOption('set-name', '', InputOption::VALUE_REQUIRED),
                                    new InputOption('set-time', '', InputOption::VALUE_REQUIRED),
                                    new InputOption('set-version', '', InputOption::VALUE_REQUIRED),
                                    new InputOption('set-build-number', '', InputOption::VALUE_REQUIRED),
                                    new InputOption('set-branch', '', InputOption::VALUE_REQUIRED),
                                    new InputOption('set-commit', '', InputOption::VALUE_REQUIRED),
                                    new InputOption('set', 's', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY),
                                ]
            ));
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): void {
        $buildInfoData = [];
        $buildInfoData[BuildInfo::NAME] = $input->getOption('set-name');
        $buildInfoData[BuildInfo::TIME] = $input->getOption('set-time');
        $buildInfoData[BuildInfo::VERSION] = $input->getOption('set-version');
        $buildInfoData[BuildInfo::BUILD_NUMBER] = $input->getOption('set-build-number');
        $buildInfoData[BuildInfo::BRANCH] = $input->getOption('set-branch');
        $buildInfoData[BuildInfo::COMMIT] = $input->getOption('set-commit');

        foreach($input->getOption('set') as $opt) {
            list($key, $value) = explode('=', $opt);
            if (is_null($value)) {
                throw new Exception('custom build info parameters must have the form "key=value": ' . $opt);
            }
            $buildInfoData['x-' . $key] = $value;
        }

        // set build time to current time, if not set
        if (!isset($buildInfoData[BuildInfo::TIME])) {
            $dt = new DateTime('now', new DateTimeZone('UTC'));
            $buildInfoData[BuildInfo::TIME] = $dt->format(DateTime::RFC3339);
        }

        $buildInfo = BuildInfo::fromArray($buildInfoData);

        foreach($input->getArgument('target') as $target) {
            $this->buildTarget($target, $buildInfo);
        }
    }

    /**
     * @param string $target
     * @param BuildInfo $buildInfo
     * @throws Exception
     */
    private function buildTarget(string $target, BuildInfo $buildInfo): void {
        $pathinfo = pathinfo($target);
        $extension = $pathinfo['extension'] ?? '';
        if (!array_key_exists($extension, $this->supportedFiletypes)) {
            throw new Exception('unsupported file type: ' . $extension);
        }

        $creator = new $this->supportedFiletypes[$extension]();
        /** @var $creator FileCreator */
        $creator->create($target, $buildInfo);
    }
}