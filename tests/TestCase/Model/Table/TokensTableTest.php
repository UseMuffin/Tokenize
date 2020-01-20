<?php
declare(strict_types=1);

namespace Muffin\Tokenize\Test\TestCase\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class TokensTableTest extends TestCase
{
    public $fixtures = [
        'plugin.Muffin/Tokenize.Tokens',
        'plugin.Muffin/Tokenize.Users',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->Tokens = TableRegistry::get('Muffin/Tokenize.Tokens');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        TableRegistry::clear();
    }

    public function testFindToken(): void
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
    public function testVerifyThrowsExceptionOnInvalidToken(): void
    {
        $this->Tokens->verify('invalid');
    }

    /**
     * @expectedException \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function testVerifyThrowsExceptionOnUsedToken(): void
    {
        $this->Tokens->verify('used');
    }

    /**
     * @expectedException \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function testVerifyThrowsExceptionOnExpiredToken(): void
    {
        $this->Tokens->verify('expired');
    }

    public function testVerify(): void
    {
        $result = $this->Tokens->verify('1736a03c6c811ef5e02a364f39521590');
        $this->assertTrue($result instanceof \Muffin\Tokenize\Model\Entity\Token);
    }

    public function testTableConfig(): void
    {
        Configure::write('Muffin/Tokenize.table', 'tokens');
        $this->Tokens->initialize([]);

        $result = $this->Tokens->getTable();
        $this->assertEquals('tokens', $result);
    }

    public function testTableConfigDefault(): void
    {
        $result = $this->Tokens->getTable();
        $this->assertEquals('tokenize_tokens', $result);
    }
}
