<?php
declare(strict_types=1);

namespace Muffin\Tokenize;

use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Routing\RouteBuilder;
use Muffin\Tokenize\Command\ClearTokensCommand;
use Muffin\Tokenize\Model\Entity\Token;

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
     * This plugin doesn't need any bootstrapping.
     *
     * @var bool
     */
    protected $bootstrapEnabled = false;

    /**
     * Add routes for the plugin.
     *
     * @param \Cake\Routing\RouteBuilder $routes The route builder to update.
     * @return void
     */
    public function routes(RouteBuilder $routes): void
    {
        $routes->plugin('Muffin/Tokenize', [], function (RouteBuilder $routes) {
            $length = Configure::read('Muffin/Tokenize.length') ?: Token::DEFAULT_LENGTH;
            $defaults = [
                'controller' => 'Tokens',
                'action' => 'verify',
            ];
            $options = [
                'token' => '[a-zA-Z0-9]{' . $length .'}',
                'pass' => ['token'],
            ];
            $routes->connect('/verify/:token', $defaults, $options);
        });
    }

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
