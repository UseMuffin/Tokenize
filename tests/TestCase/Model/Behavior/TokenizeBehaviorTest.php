<?php
declare(strict_types=1);

namespace Muffin\Tokenize\Test\TestCase\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Muffin\Tokenize\Model\Behavior\TokenizeBehavior;
use Muffin\Tokenize\Model\Entity\Token;

class TokenizeBehaviorTest extends TestCase
{
    public $fixtures = [
        'plugin.Muffin/Tokenize.Users',
        'plugin.Muffin/Tokenize.Tokens',
    ];

    public function tearDown(): void
    {
        parent::tearDown();
        TableRegistry::clear();
    }

    public function testInitialize(): void
    {
        $table = TableRegistry::get('Users', ['table' => 'tokenize_users']);
        $table->addBehavior('Muffin/Tokenize.Tokenize', [
            'fields' => ['email', 'password'],
            'implementedEvents' => [
                'Model.beforeSave' => 'beforeSave',
                'Model.afterSave' => 'afterSave',
            ],
        ]);

        $result = $table->associations()->keys();
        $expected = ['tokens'];
        $this->assertEquals($expected, $result);

        $result = $table->behaviors()->Tokenize->implementedEvents();
        $expected = [
            'Model.beforeSave' => 'beforeSave',
            'Model.afterSave' => 'afterSave',
        ];
        $this->assertEquals($expected, $result);
    }

    public function testBeforeSaveWithNewEntity(): void
    {
        $fields = ['email' => 'jadb@cakephp.org'];
        $entity = new Entity($fields);
        $options = new \ArrayObject();
        $config = ['implementedEvents' => ['Model.afterSave' => 'afterSave'], 'fields' => 'email'];

        $table = $this->getMockBuilder('Cake\ORM\Table')
            ->setMethods(['dispatchEvent'])
            ->setConstructorArgs([[
                'table' => 'tokenize_users',
                'alias' => 'Users',
            ]])
            ->getMock();
        $table->setPrimaryKey('id');

        $behavior = $this->getMockBuilder('Muffin\Tokenize\Model\Behavior\TokenizeBehavior')
            ->setMethods(['fields', 'tokenize'])
            ->setConstructorArgs([$table, $config])
            ->getMock();
        $behavior->expects($this->once())
            ->method('fields')
            ->with($entity)
            ->will($this->returnValue($fields));

        $behavior->beforeSave(new Event('Model.beforeSave'), $entity, $options, true);

        $this->assertEquals($fields, $options['tokenize_fields']);
    }

    public function testBeforeSaveWithExistingEntity(): void
    {
        $id = 1;
        $fields = ['email' => 'jadb@cakephp.org'];
        $entity = new Entity(compact('id'), ['markClean' => true, 'markNew' => false]);
        $entity->set($fields);
        $options = new \ArrayObject();
        $token = 'foo';

        $table = $this->getMockBuilder('Cake\ORM\Table')
            ->setMethods(['dispatchEvent'])
            ->setConstructorArgs([[
                'table' => 'tokenize_users',
                'alias' => 'Users',
            ]])
            ->getMock();
        $table->setPrimaryKey('id');
        $table->expects($this->once())
            ->method('dispatchEvent')
            ->with('Model.afterTokenize', compact('entity', 'token'));

        $behavior = $this->getMockBuilder('Muffin\Tokenize\Model\Behavior\TokenizeBehavior')
            ->setMethods(['fields', 'tokenize'])
            ->setConstructorArgs([$table])
            ->getMock();
        $behavior->expects($this->once())
            ->method('fields')
            ->with($entity)
            ->will($this->returnValue($fields));
        $behavior->expects($this->once())
            ->method('tokenize')
            ->with($id, $fields)
            ->will($this->returnValue($token));

        $behavior->beforeSave(new Event('Model.beforeSave'), $entity, $options, true);
    }

    public function testAfterSave(): void
    {
        $id = 1;
        $fields = ['email' => 'jadb@cakephp.org'];
        $entity = new Entity(compact('id'), ['markClean' => true, 'markNew' => false]);
        $token = 'foo';
        $options = new \ArrayObject(['tokenize_fields' => $fields]);

        $table = $this->getMockBuilder('Cake\ORM\Table')
            ->setMethods(['dispatchEvent'])
            ->setConstructorArgs([[
                'table' => 'tokenize_users',
                'alias' => 'Users',
            ]])
            ->getMock();
        $table->setPrimaryKey('id');
        $table->expects($this->once())
            ->method('dispatchEvent')
            ->with('Model.afterTokenize', compact('entity', 'token'));

        $behavior = $this->getMockBuilder('Muffin\Tokenize\Model\Behavior\TokenizeBehavior')
            ->setMethods(['fields', 'tokenize'])
            ->setConstructorArgs([$table])
            ->getMock();
        $behavior->expects($this->once())
            ->method('tokenize')
            ->with($id, $fields)
            ->will($this->returnValue($token));

        $behavior->afterSave(new Event('Model.afterSave'), $entity, $options);
    }

    public function testFields(): void
    {
        $this->markTestIncomplete('Not tested yet.');
    }

    public function testTokenize(): void
    {
        $id = 1;
        $data = ['email' => 'jadb@cakephp.org'];
        $tokenData = [
            'foreign_alias' => 'Users',
            'foreign_table' => 'tokenize_users',
            'foreign_key' => $id,
            'foreign_data' => $data,
        ];
        $token = new Token($tokenData, ['markNew' => false, 'markClean' => true]);

        $table = $this->getMockBuilder('Cake\ORM\Table')
            ->setMethods(['dispatchEvent'])
            ->setConstructorArgs([[
                'table' => 'tokenize_users',
                'alias' => 'Users',
            ]])
            ->getMock();
        $table->setPrimaryKey('id');

        $table->Tokens = $this->getMockBuilder('Muffin\Tokenize\Model\Table\TokensTable')
            ->setMethods(['newEntity', 'save'])
            ->setConstructorArgs([])
            ->getMock();
        $table->Tokens->expects($this->once())
            ->method('newEntity')
            ->with($tokenData)
            ->will($this->returnValue($token));
        $table->Tokens->expects($this->once())
            ->method('save')
            ->with($token)
            ->will($this->returnValue(true));

        $behavior = new TokenizeBehavior($table, ['associationClass' => 'Tokens']);

        $result = $behavior->tokenize(1, $data);
        $this->assertEquals($token, $result);
    }
}
