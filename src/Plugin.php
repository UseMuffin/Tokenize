<?php
declare(strict_types=1);

namespace Muffin\Tokenize;

use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use Muffin\Tokenize\Command\ClearTokensCommand;

/**
 * Plugin class for tokenize
 */
class Plugin extends BasePlugin
{
    /**
     * Plugin name.
     *
     * @var string
     */
    protected $name = 'Tokenize';

    /**
     * Add console commands for the plugin.
     *
     * @param \Cake\Console\CommandCollection $commands The command collection to update
     * @return \Cake\Console\CommandCollection
     */
    public function console(CommandCollection $commands): CommandCollection
    {
        return $commands->add('clear_tokens', ClearTokensCommand::class);
    }

}
