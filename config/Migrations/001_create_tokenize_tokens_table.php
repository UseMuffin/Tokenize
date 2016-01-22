<?php

use Migrations\AbstractMigration;
use Phinx\Db\Table\Index;

class CreateTokenizeTokensTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('tokenize_tokens', [
            'id' => false,
            'primary_key' => 'id',
        ]);

        $table->addColumn('id', 'integer', ['auto_increment' => true, 'signed' => true]);
        $table->addColumn('token', 'string');
        $table->addColumn('foreign_alias', 'string');
        $table->addColumn('foreign_table', 'string');
        $table->addColumn('foreign_key', 'string');
        $table->addColumn('foreign_data', 'text');
        $table->addColumn('status', 'boolean');
        $table->addColumn('expired', 'datetime');
        $table->addColumn('created', 'datetime');
        $table->addColumn('modified', 'datetime');

        $table->addPrimaryKey('id');
        $table->addIndex('TOKENIZE_TOKEN', ['columns' => 'token', 'type' => Index::UNIQUE]);
        $table->addIndex('TOKENIZE_STATUS', ['columns' => 'status']);
        $table->addIndex('TOKENIZE_MODEL', ['columns' => [
            'foreign_alias',
            'foreign_table',
            'foreign_key',
        ]]);

        $table->create();
    }

}
