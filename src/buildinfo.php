<?php

$autoload = false;
foreach([__DIR__ . '/../../../../vendor/autoload.php', __DIR__ . '/../vendor/autoload.php'] as $autoloadFile) {
    if (file_exists($autoloadFile)) {
        /** @noinspection PhpIncludeInspection */
        require_once $autoloadFile;
        $autoload = true;
        break;
    }
}

if ($autoload !== true) {
    fwrite(STDERR, "ERROR: could not find autoload file\n");
    exit(1);
}

use Fostam\BuildInfoCreate\Command\Create;
use Symfony\Component\Console\Application;

$application = new Application();
$application->setName('BuildInfo');
$application->setVersion('1.0.0');
$application->add(new Create());
try {
    $application->run();
}
catch (Exception $e) {
    fwrite(STDERR, "ERROR: " . $e->getMessage() . "\n");
    exit(1);
}
exit(0);