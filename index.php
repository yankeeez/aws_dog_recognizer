<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

return function ($event) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $client = new Aws\Sfn\SfnClient([
        'region'  => $_ENV['REGION'],
        'version' => $_ENV['VERSION']
    ]);

    $params = [
        'input' => json_encode($event),
        'stateMachineArn' => $_ENV['IMAGE_RECOGNIZE_SM_ARN']
    ];
    $client->startExecution($params);

    return $event;
};
