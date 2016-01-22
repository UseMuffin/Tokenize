<?php
namespace Muffin\Tokenize\Controller;

use Cake\Controller\Controller;

class TokensController extends Controller
{

    public function verify()
    {
        $this->loadModel('Muffin/Tokenize.Tokens')->verify($this->request->param('token'));
    }
}
