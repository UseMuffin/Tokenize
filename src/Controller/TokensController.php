<?php
namespace Muffin\Tokenize\Controller;

use Cake\Controller\Controller;

class TokensController extends Controller
{

    /**
     * Verify action
     *
     * @return void
     */
    public function verify()
    {
        $this->loadModel('Muffin/Tokenize.Tokens')
            ->verify($this->request->getParam('token'));
    }
}
