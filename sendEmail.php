<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

return function ($event) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $key = $event['Records'][0]['s3']['object']['key'];
    $bucket = $event['Records'][0]['s3']['bucket']['name'];
    $sender = 'sender@example.com';
    $sendername = 'Sender Name';
    $recipient = $_ENV['RECEPIENT_EMAIL'];
    $configset = 'ConfigSet';
    $subject = 'AWS image info';

    $htmlbody = <<<EOD
<html>
<head></head>
<body>
<h1>Hello!</h1>
<p>Dog not found!</p>
</body>
</html>
EOD;

    $textbody = <<<EOD
Hello,
Dog not found!
EOD;

    $att = downloadFile($key, $bucket);

    $client = new Aws\Ses\SesClient([
        'version'=> $_ENV['VERSION'],
        'region' => $_ENV['SES_REGION']
    ]);

    $mail = new PHPMailer\PHPMailer\PHPMailer();

    $mail->setFrom($sender, $sendername);
    $mail->addAddress($recipient);
    $mail->Subject = $subject;
    $mail->Body = $htmlbody;
    $mail->AltBody = $textbody;
    $mail->addAttachment($att);
    $mail->addCustomHeader('X-SES-CONFIGURATION-SET', $configset);

    if (!$mail->preSend()) {
        echo $mail->ErrorInfo;
    } else {
        $message = $mail->getSentMIMEMessage();
    }

    try {
        $result = $client->sendRawEmail([
            'RawMessage' => [
                'Data' => $message
            ]
        ]);
        $messageId = $result->get('MessageId');
        if ($messageId) {
            unlink(sprintf('/tmp/%s', $key));
        }
    } catch (Exception $error) {
    }
};

function downloadFile($key, $bucket)
{    $client = new Aws\S3\S3Client([
        'region'  => $_ENV['REGION'],
        'version' => $_ENV['VERSION']
    ]);
    return $client->getObject(array(
        'Bucket' => $bucket,
        'Key'    => $key,
        'SaveAs' => sprintf('/tmp/%s', $key)
    ));
}
