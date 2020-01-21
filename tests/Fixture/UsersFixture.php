<?php
declare(strict_types=1);

namespace Muffin\Tokenize\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersFixture extends TestFixture
{
    public $table = 'tokenize_users';

    public $fields = [
        'id' => ['type' => 'integer', 'autoIncrement' => true],
        'name' => ['type' => 'string'],
        'email' => ['type' => 'string'],
        'password' => ['type' => 'string'],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ],
    ];

    public $records = [
        ['name' => 'Foo', 'email' => 'f@o.bar', 'password' => ''],
    ];
}
