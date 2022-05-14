<?php

use ExampleRS\Helpers\ExampleHelper;
use ExampleRS\Listeners\ExampleListener;

return [
    'providers' => [
    ],

    'aliases' => [
        "ExampleHelper" => ExampleHelper::class,
    ],
    
    'listeners' => [
        ExampleListener::class,
    ],
]

?>
