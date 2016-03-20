<?php

use Cake\Core\Configure;
use Cake\Database\Type;

Configure::write('Muffin/Tokenize', [
    'lifetime' => '3 days',
    'length' => 32,
]);

if (!Type::map('json')) {
    Type::map('json', 'Muffin\Tokenize\Database\Type\JsonType');
}
