<?php
declare(strict_types=1);

namespace Muffin\Tokenize\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;

/**
 * Command for `clear_tokens`
 */
class ClearTokensCommand extends Command
{
    /**
     * Deletes all expired/used tokens.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io   The console io
     *
     * @return null|void|int The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $count = $this->loadModel('Muffin/Tokenize.Tokens')->deleteAllExpiredOrUsed();
        $io->out(__n('One token deleted', '{0} tokens deleted', $count, $count));

        return 0;
    }
}
