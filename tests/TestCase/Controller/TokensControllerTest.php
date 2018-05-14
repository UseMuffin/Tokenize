<?php
namespace Muffin\Tokenize\Test\TestCase\Controller;

use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Muffin\Tokenize\Controller\TokensController;

class TokensControllerTest extends TestCase
{
    /**
     * @group unit
     */
    public function testVerify()
    {
        $request = new ServerRequest();
        $controller = new TokensController($request->withParam('token', 'foo'), new Response());
        $controller->Tokens = $this->getMockForModel('Muffin/Tokenize.Tokens', ['verify']);
        $controller->Tokens->expects($this->once())
            ->method('verify')
            ->with('foo');

        $controller->verify();
    }
}
