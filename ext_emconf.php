<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Matomo Widgets',
    'description' => 'Dashboard widgets with Matomo reports',
    'category' => 'module',
    'author' => 'Chris Müller',
    'author_email' => 'typo3@brotkrueml.dev',
    'state' => 'stable',
    'version' => '4.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.6-14.3.99',
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
