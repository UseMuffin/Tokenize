<?php
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
     * @param array $config
     */
    public function initialize(array $config)
    {
        $this->verifyConfig();

        $this->_table->hasMany($this->config('associationAlias'), [
            'className' => TokensTable::class,
            'foreignKey' => 'foreign_key',
            'conditions' => [
                'foreign_alias' => $this->_table->alias(),
                'foreign_table' => $this->_table->table(),
            ],
            'dependent' => true,
        ]);
    }

    /**
     * @param \Cake\Event\Event $event
     * @param \Cake\Datasource\EntityInterface $entity
     * @param \ArrayObject $options
     * @param $primary
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options, $primary)
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

        $token = $this->tokenize($entity->{$this->_table->primaryKey()}, $data);
        $this->_table->dispatchEvent('Model.afterTokenize', compact('entity', 'token'));
    }

    /**
     * @param \Cake\Event\Event $event
     * @param \Cake\Datasource\EntityInterface $entity
     * @param \ArrayObject $options
     */
    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        if (empty($options['tokenize_fields'])) {
            return;
        }

        $token = $this->tokenize($entity->{$this->_table->primaryKey()}, $options['tokenize_fields']);
        $this->_table->dispatchEvent('Model.afterTokenize', compact('entity', 'token'));
    }

    /**
     * Creates a token for a data sample.
     *
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function tokenize($id, array $data)
    {
        $assoc = $this->config('associationAlias');

        $tokenData = [
            'foreign_alias' => $this->_table->alias(),
            'foreign_table' => $this->_table->table(),
            'foreign_key' => $id,
            'foreign_data' => $data,
        ];

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
     * @param \Cake\Datasource\EntityInterface $entity
     * @return array
     */
    public function fields(EntityInterface $entity)
    {
        $fields = [];
        foreach ((array)$this->config('fields') as $field) {
            if (!$entity->dirty($field)) {
                continue;
            }
            $fields[$field] = $entity->$field;
            $entity->dirty($field, false);
        }
        return $fields;
    }
}
