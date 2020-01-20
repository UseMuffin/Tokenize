<?php
declare(strict_types=1);

namespace Muffin\Tokenize\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Muffin\Tokenize\Model\Table\TokensTable;

class TokenizeBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'fields' => null,
        'associationAlias' => 'Tokens',
        'implementedEvents' => [
            'Model.beforeSave' => 'beforeSave',
        ]
    ];

    /**
     * Verifies the configuration and associates the table to the
     * `tokenize_tokens` table.
     *
     * @param array $config Config
     *
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->verifyConfig();

        $options = [
            'className' => 'Muffin/Tokenize.Tokens',
            'foreignKey' => 'foreign_key',
            'conditions' => [
                'foreign_alias' => $this->_table->getAlias(),
                'foreign_table' => $this->_table->getTable(),
            ],
            'dependent' => true,
        ];
        $this->_table->hasMany($this->getConfig('associationAlias'), $options);
    }

    /**
     * beforeSave callback.
     *
     * @param \Cake\Event\Event                $event   Event
     * @param \Cake\Datasource\EntityInterface $entity  Entity
     * @param \ArrayObject                     $options Options
     *
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options): void
    {
        $data = $this->fields($entity);

        if (empty($data)) {
            return;
        }

        if ($entity->isNew()) {
            if (array_key_exists('Model.afterSave', $this->implementedEvents())) {
                $options['tokenize_fields'] = $data;
            }

            return;
        }

        $token = $this->tokenize($entity->{$this->_table->getPrimaryKey()}, $data);
        $this->_table->dispatchEvent('Model.afterTokenize', compact('entity', 'token'));
        return;
    }

    /**
     * afterSave callback.
     *
     * @param \Cake\Event\Event                $event   Event
     * @param \Cake\Datasource\EntityInterface $entity  Entity
     * @param \ArrayObject                     $options Options
     *
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options): void
    {
        if (empty($options['tokenize_fields'])) {
            return;
        }

        $token = $this->tokenize($entity->{$this->_table->getPrimaryKey()}, $options['tokenize_fields']);
        $this->_table->dispatchEvent('Model.afterTokenize', compact('entity', 'token'));
        return;
    }

    /**
     * Creates a token for a data sample.
     *
     * @param int|string $id   Id
     * @param array      $data Data
     *
     * @return mixed
     */
    public function tokenize($id, array $data = [])
    {
        $assoc = $this->getConfig('associationAlias');

        $tokenData = [
            'foreign_alias' => $this->_table->getAlias(),
            'foreign_table' => $this->_table->getTable(),
            'foreign_key' => $id,
            'foreign_data' => $data,
        ];
        $tokenData = array_filter($tokenData);
        if (!isset($tokenData['foreign_data'])) {
            $tokenData['foreign_data'] = [];
        }

        $table = $this->_table->$assoc;
        $token = $table->newEntity($tokenData);

        if (!$table->save($token)) {
            throw new \RuntimeException();
        }

        return $token;
    }

    /**
     * Returns fields that have been marked as protected.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity
     *
     * @return array
     */
    protected function fields(EntityInterface $entity): array
    {
        $fields = [];
        foreach ((array)$this->getConfig('fields') as $field) {
            if (!$entity->isDirty($field)) {
                continue;
            }
            $fields[$field] = $entity->$field;
            $entity->setDirty($field, false);
        }

        return $fields;
    }
}
