<?php
declare(strict_types=1);

namespace Muffin\Tokenize\Model\Table;

use Cake\Core\Configure;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use DateTime;
use Muffin\Tokenize\Model\Entity\Token;

class TokensTable extends Table
{
    public const DEFAULT_TABLE = 'tokenize_tokens';

    /**
     * Initialize table
     *
     * @param array $config Config
     *
     * @return void
     */
    public function initialize(array $config): void
    {
        $table = Configure::read('Muffin/Tokenize.table', self::DEFAULT_TABLE);

        $this->setTable($table);
        $this->setPrimaryKey('id');
        $this->setDisplayField('token');

        $this->addBehavior('Timestamp');
    }

    /**
     * Custom finder "token"
     *
     * @param \Cake\ORM\Query $query   Query
     * @param array $options           Options
     *
     * @return \Cake\ORM\Query
     */
    public function findToken(Query $query, array $options): Query
    {
        $options += [
            'token' => null,
            'expired >' => new DateTime(),
            'status' => false,
        ];

        return $query->where($options);
    }

    /**
     * Delete all expired or used tokens.
     *
     * @return int
     */
    public function deleteAllExpiredOrUsed(): int
    {
        return $this->deleteAll(
            ['OR' => [
                'expired <' => new DateTime(),
                'status' => true,
            ]]
        );
    }

    /**
     * Verify token
     *
     * @param string $token Token
     *
     * @return null|\Cake\Datasource\EntityInterface
     */
    public function verify($token): ?EntityInterface
    {
        $result = $this->find('token', compact('token'))->first();
        if (!$result) {
            return null;
        }

        $event = $this->dispatchEvent('Muffin/Tokenize.beforeVerify', ['token' => $result]);
        if ($event->isStopped()) {
            return null;
        }

        if (!empty($result['foreign_data'])) {
            $table = $this->foreignTable($result);
            $fields = $result['foreign_data'];
            $conditions = [(string)$table->getPrimaryKey() => $result['foreign_key']];
            $table->updateAll($fields, $conditions);
        }

        $result->set('status', true);
        $this->save($result);

        $this->dispatchEvent('Muffin/Tokenize.afterVerify', ['token' => $result]);

        return $result;
    }

    /**
     * Fetch the associated foreign table based on the token's foreign_alias
     *
     * @param \Muffin\Tokenize\Model\Entity\Token $token Token entity
     *
     * @return \Cake\ORM\Table
     */
    protected function foreignTable(Token $token): Table
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
     * Override this function in order to alter the schema used by this table.
     * This function is only called after fetching the schema out of the database.
     * If you wish to provide your own schema to this table without touching the
     * database, you can override schema() or inject the definitions though that
     * method.
     *
     * ### Example:
     *
     * ```
     * protected function _initializeSchema(\Cake\Database\Schema\TableSchemaInterface $schema) {
     *  $schema->setColumnType('preferences', 'json');
     *  return $schema;
     * }
     * ```
     *
     * @param \Cake\Database\Schema\TableSchemaInterface $schema Schema
     *
     * @return \Cake\Database\Schema\TableSchemaInterface
     */
    protected function _initializeSchema(TableSchemaInterface $schema): TableSchemaInterface
    {
        $schema->setColumnType('foreign_data', 'json');

        return $schema;
    }
}
