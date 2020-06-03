<?php

return [
    'services' => [
        'singleton' => [ // Include Singleton services 
            'Core\\Database\\Database' => 'Core\\Database\\DatabaseInterface',
        ],

        'basic' => [ //Include services that are not Singleton
            'Core\\Config\\Config' => 'Core\\Config\\ConfigInterface',
            'Core\\Request\\IncomingRequest' => 'Core\\Request\\RequestInterface',
            'Core\\Response\\Response' => 'Core\\Response\\ResponseInterface',
            'Core\\Router\\Router' => 'Core\\Router\\RouterInterface',
        ],
    ]
];