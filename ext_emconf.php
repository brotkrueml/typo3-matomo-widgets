<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Matomo Widgets',
    'description' => 'Dashboard widgets showing Matomo reports',
    'category' => 'module',
    'author' => 'Chris Müller',
    'author_email' => 'typo3@krue.ml',
    'state' => 'stable',
    'version' => '1.0.0-dev',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.11-11.5.99',
            'dashboard' => '',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => ['Brotkrueml\\MatomoWidgets\\' => 'Classes']
    ],
];
