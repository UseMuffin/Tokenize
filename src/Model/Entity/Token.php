<?php
namespace Muffin\Tokenize\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;
use Cake\Utility\Security;

/**
 * Token Entity
 *
 * @property int $id
 * @property string $token
 * @property string $foreign_alias
 * @property string $foreign_table
 * @property string $foreign_key
 * @property array $foreign_data
 * @property bool $status
 */
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
        $lifetime = Configure::read('Muffin/Tokenize.lifetime', self::DEFAULT_LIFETIME);
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
     * @param int|null $length Token length
     * @return string
     * @see http://stackoverflow.com/a/29137661/2020428
     */
    public static function random($length = null)
    {
        if ($length === null) {
            $length = Configure::read('Muffin/Tokenize.length', self::DEFAULT_LENGTH);
        }

        return bin2hex(Security::randomBytes($length / 2));
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
