<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Matomo Widgets',
    'description' => 'Dashboard widgets with Matomo reports',
    'category' => 'module',
    'author' => 'Chris Müller',
    'author_email' => 'typo3@krue.ml',
    'state' => 'stable',
    'version' => '1.1.0-dev',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.15-11.5.99',
            'dashboard' => '',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => ['Brotkrueml\\MatomoWidgets\\' => 'Classes']
    ],
];
