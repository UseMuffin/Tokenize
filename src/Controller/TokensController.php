<?php
declare(strict_types=1);

namespace Muffin\Tokenize\Controller;

use Cake\Controller\Controller;

class TokensController extends Controller
{
    /**
     * Verify action
     *
     * @return void
     */
    public function verify(): void
    {
        $this->loadModel('Muffin/Tokenize.Tokens')
            ->verify($this->request->getParam('token'));
    }
}
