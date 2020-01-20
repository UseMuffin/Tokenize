<?php
declare(strict_types=1);

namespace Tokenize\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Tokenize\Command\ClearTokensCommand;

/**
 * Tokenize\Command\ClearTokensCommand Test Case
 *
 * @uses \Tokenize\Command\ClearTokensCommand
 */
class ClearTokensCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
