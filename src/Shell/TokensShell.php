<?php
namespace Muffin\Tokenize\Shell;

use Cake\Console\Shell;

class TokensShell extends Shell
{
    /**
     * Deletes all expired/used tokens.
     *
     * @return void
     */
    public function clear()
    {
        $count = $this->loadModel('Muffin/Tokenize.Tokens')->deleteAllExpiredOrUsed();
        $this->out(__n('One token deleted', '{0} tokens deleted', $count, $count));
    }
}
