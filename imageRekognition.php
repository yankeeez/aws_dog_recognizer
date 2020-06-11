<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

return function ($event) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $client = new Aws\Rekognition\RekognitionClient([
        'region'  => $_ENV['REGION'],
        'version' => $_ENV['VERSION']
    ]);

    $result = $client->detectLabels([
        'Image' => [
            'S3Object' => [
            'Bucket' => $_ENV['S3_BUCKET'],
            'Name' => $event['Records'][0]['s3']['object']['key']
        ],
    ],
    'MinConfidence' => 85,
    ]);
    return $result->get('Labels');
};
