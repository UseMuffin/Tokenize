<?php
declare(strict_types=1);

namespace Muffin\Tokenize\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class TokensFixture extends TestFixture
{

    public $table = 'tokenize_tokens';

    public $fields = [
        'id' => ['type' => 'integer', 'autoIncrement' => true],
        'token' => ['type' => 'string'],
        'foreign_alias' => ['type' => 'string'],
        'foreign_table' => ['type' => 'string'],
        'foreign_key' => ['type' => 'string'],
        'foreign_data' => ['type' => 'text'],
        'status' => ['type' => 'boolean'],
        'expired' => ['type' => 'datetime'],
        'created' => ['type' => 'datetime'],
        'modified' => ['type' => 'datetime'],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
        ]
    ];

    public $records = [
        [
            'token' => 'used',
            'foreign_alias' => 'Users',
            'foreign_table' => 'tokenize_users',
            'foreign_key' => 1,
            'foreign_data' => ['email' => 'jadb@cakephp.org'],
            'status' => true,
        ],
        [
            'token' => 'expired',
            'foreign_alias' => 'Users',
            'foreign_table' => 'tokenize_users',
            'foreign_key' => 1,
            'foreign_data' => ['email' => 'jadb@cakephp.org'],
            'status' => false,
        ],
        [
            'token' => '1736a03c6c811ef5e02a364f39521590',
            'foreign_alias' => 'Users',
            'foreign_table' => 'tokenize_users',
            'foreign_key' => 1,
            'foreign_data' => ['email' => 'jadb@cakephp.org'],
            'status' => false,
        ],
    ];

    public function init()
    {
        $format = 'Y-m-d H:i:s';
        foreach ($this->records as &$record) {
            $record['foreign_data'] = json_encode($record['foreign_data']);
            $record['created'] = $record['modified'] = date($format, strtotime('-2 days'));
            $record['expired'] = date($format, strtotime('1 day'));
            if ($record['token'] === 'expired') {
                $record['expired'] = date($format, strtotime('-1 day'));
            }
        }
        parent::init();
    }
}
