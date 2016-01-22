<?php
namespace Muffin\Tokenize\Test\TestCase\Controller;

use Cake\Network\Response;
use Cake\TestSuite\TestCase;
use Muffin\Tokenize\Controller\TokensController;

class TokensControllerTest extends TestCase
{
    /**
     * @group unit
     */
    public function testVerify()
    {
        $request = $this->getMock('Cake\Network\Request', ['param']);
        $request->expects($this->once())
            ->method('param')
            ->with('token')
            ->will($this->returnValue('foo'));

        $controller = new TokensController($request, new Response());
        $controller->Tokens = $this->getMockForModel('Muffin/Tokenize.Tokens', ['verify']);
        $controller->Tokens->expects($this->once())
            ->method('verify')
            ->with('foo');

        $controller->verify();
    }
}
