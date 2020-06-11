<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

return function ($event) {
    $labels = $event['results']['labels'];

    $isDog = isDog($labels);
    return $isDog;
};

function isDog($labels)
{
    $result = false;
    foreach ($labels as $label)
    {
        if ($label['Name'] == 'Dog') {
            $result = true;
            break;
        }
    }
    return $result;
}
