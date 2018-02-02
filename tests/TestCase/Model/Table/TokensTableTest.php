<?php
namespace Muffin\Tokenize\Test\TestCase\Model\Table;

use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Muffin\Tokenize\Model\Table\TokensTable;

class TokensTableTest extends TestCase
{
    public $fixtures = [
        'plugin.Muffin/Tokenize.Tokens',
        'plugin.Muffin/Tokenize.Users',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Tokens = TableRegistry::get('Muffin/Tokenize.Tokens');
    }

    public function tearDown()
    {
        parent::tearDown();
        TableRegistry::clear();
    }

    public function testFindToken()
    {
        $result = $this->Tokens->find('token', ['token' => 'invalid']);
        $this->assertCount(0, $result);

        $result = $this->Tokens->find('token', ['token' => 'expired']);
        $this->assertCount(0, $result);

        $result = $this->Tokens->find('token', ['token' => 'used']);
        $this->assertCount(0, $result);

        $result = $this->Tokens->find('token', ['token' => '1736a03c6c811ef5e02a364f39521590']);
        $this->assertCount(1, $result);
    }

    /**
     * @expectedException \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function testVerifyThrowsExceptionOnInvalidToken()
    {
        $this->Tokens->verify('invalid');
    }

    /**
     * @expectedException \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function testVerifyThrowsExceptionOnUsedToken()
    {
        $this->Tokens->verify('used');
    }

    /**
     * @expectedException \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function testVerifyThrowsExceptionOnExpiredToken()
    {
        $this->Tokens->verify('expired');
    }

    public function testVerify()
    {
        $result = $this->Tokens->verify('1736a03c6c811ef5e02a364f39521590');
        $this->assertTrue($result instanceof \Muffin\Tokenize\Model\Entity\Token);
    }

    public function testTableConfig()
    {
        Configure::write('Muffin/Tokenize.table', 'tokens');
        $this->Tokens->initialize([]);

        $result = $this->Tokens->getTable();
        $this->assertEquals('tokens', $result);
    }

    public function testTableConfigDefault()
    {
        $result = $this->Tokens->getTable();
        $this->assertEquals('tokenize_tokens', $result);
    }
}
