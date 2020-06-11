<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

return function ($event) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $client = new Aws\DynamoDb\DynamoDbClient([
        'region'  => $_ENV['REGION'],
        'version' => $_ENV['VERSION']
    ]);

    $result = $client->putItem([
        'TableName' => $_ENV['DYNAMODB_TABLE_NAME'],
        'Item' => [
            'name' => [
                'S' => $event['Records'][0]['s3']['object']['key']
            ],
            'timestamp' => [
                'S' => (string)time()
            ],
            'labels' => [
                'S' => json_encode($event['results']['labels'])
            ]
        ]
    ]);

    return $event;
};
