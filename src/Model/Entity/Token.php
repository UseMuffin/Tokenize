<?php
namespace Muffin\Tokenize\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;

class Token extends Entity
{
    const DEFAULT_LIFETIME = '3 days';

    const DEFAULT_LENGTH = 32;

    /**
     * Token constructor.
     *
     * @param array $properties Properties
     * @param array $options Options
     */
    public function __construct(array $properties = [], array $options = [])
    {
        $lifetime = Configure::read('Muffin/Tokenize.lifetime') ?: self::DEFAULT_LIFETIME;
        $properties += [
            'token' => self::random(),
            'status' => false,
            'expired' => date('Y-m-d H:i:s', strtotime($lifetime))
        ];
        parent::__construct($properties, $options);
    }

    /**
     * Creates a secure random token.
     *
     * @param null $length Token length
     * @return string
     * @see http://stackoverflow.com/a/29137661/2020428
     */
    public static function random($length = null)
    {
        if ($length === null) {
            $length = Configure::read('Muffin/Tokenize.length') ?: self::DEFAULT_LENGTH;
        }

        $function = 'openssl_random_pseudo_bytes';
        if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
            $function = 'random_bytes';
        }

        return bin2hex(call_user_func($function, $length / 2));
    }

    /**
     * To string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->token;
    }
}
