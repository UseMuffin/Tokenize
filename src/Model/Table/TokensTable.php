<?php
namespace Muffin\Tokenize\Model\Table;

use Cake\Database\Schema\Table as Schema;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use DateTime;

class TokensTable extends Table
{

    /**
     * @param array $config
     */
    public function initialize(array $config)
    {
        $this->table('tokenize_tokens');
        $this->primaryKey('id');
        $this->displayField('token');
    }

    public function findToken(Query $query, array $options)
    {
        $options += [
            'token' => null,
            'expired >' => new DateTime(),
            'status' => false
        ];

        return $query->where($options);
    }

    public function verify($token)
    {
        $result = $this->find('token', compact('token'))->firstOrFail();

        $event = $this->dispatchEvent('Muffin/Tokenize.beforeVerify', ['token' => $result]);
        if ($event->isStopped()) {
            return false;
        }

        if (!empty($result['foreign_data'])) {
            $table = $this->foreignTable($result);
            $fields = $result['foreign_data'];
            $conditions = [$table->primaryKey() => $result['foreign_key']];
            $table->updateAll($fields, $conditions);
        }

        $result->set('status', true);
        $this->save($result);

        $this->dispatchEvent('Muffin/Tokenize.afterVerify', ['token' => $result]);
    }

    protected function foreignTable(Token $token)
    {
        $options = [];
        if (!TableRegistry::exists($token['foreign_alias'])) {
            $options = [
                'table' => $token['foreign_table'],
            ];
        }

        return TableRegistry::get($token['foreign_alias'], $options);
    }

    /**
     * @param \Cake\Database\Schema\Table $schema
     * @return \Cake\Database\Schema\Table
     */
    protected function _initializeSchema(Schema $schema)
    {
        $schema->columnType('foreign_data', 'json');
        return $schema;
    }
}
