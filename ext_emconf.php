<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Matomo Widgets',
    'description' => 'Dashboard widgets with Matomo reports',
    'category' => 'module',
    'author' => 'Chris Müller',
    'author_email' => 'typo3@krue.ml',
    'state' => 'stable',
    'version' => '2.1.0',
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-0.0.0',
            'typo3' => '11.5.0-12.4.99',
            'dashboard' => '',
        ],
        'conflicts' => [],
        'suggests' => [
            'matomo_integration' => '',
        ],
    ],
    'autoload' => [
        'psr-4' => ['Brotkrueml\\MatomoWidgets\\' => 'Classes']
    ],
];
