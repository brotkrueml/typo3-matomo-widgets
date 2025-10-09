<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Matomo Widgets',
    'description' => 'Dashboard widgets with Matomo reports',
    'category' => 'module',
    'author' => 'Chris Müller',
    'author_email' => 'typo3@brotkrueml.dev',
    'state' => 'stable',
    'version' => '3.4.0',
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-0.0.0',
            'typo3' => '12.4.0-13.4.99',
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
