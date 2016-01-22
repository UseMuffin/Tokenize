<?php

use Cake\Core\Configure;
use Cake\Routing\Router;
use Muffin\Tokenize\Model\Entity\Token;

Router::plugin('Muffin/Tokenize', function ($routes) {
    $length = Configure::read('Muffin/Tokenize.length') ?: Token::DEFAULT_LENGTH;
    $defaults = [
        'controller' => 'Tokens',
        'action' => 'verify',
    ];
    $options = [
        'token' => '[a-zA-Z0-9]{' . $length .'}',
        'pass' => ['token'],
    ];
    $routes->connect('/verify/:token', $defaults, $options);
});
