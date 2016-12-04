<?php
namespace Muffin\Tokenize\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type;
use PDO;

class JsonType extends Type
{

    /**
     * Convert string values to PHP arrays.
     *
     * @param mixed $value The value to convert.
     * @param \Cake\Database\Driver $driver The driver instance to convert with.
     * @return string|null|array
     */
    public function toPHP($value, Driver $driver)
    {
        if ($value === null) {
            return null;
        }

        return json_decode($value, true);
    }

    /**
     * Marshalls request data into a JSON compatible structure.
     *
     * @param mixed $value The value to convert.
     * @return mixed Converted value.
     */
    public function marshal($value)
    {
        if (is_array($value) || $value === null) {
            return $value;
        }

        return json_decode($value, true);
    }

    /**
     * Convert a value data into a JSON string
     *
     * @param mixed $value The value to convert.
     * @param \Cake\Database\Driver $driver The driver instance to convert with.
     * @return string|null
     */
    public function toDatabase($value, Driver $driver)
    {
        return json_encode($value);
    }

    /**
     * Get the correct PDO binding type for string data.
     *
     * @param mixed $value The value being bound.
     * @param \Cake\Database\Driver $driver The driver.
     * @return int
     */
    public function toStatement($value, Driver $driver)
    {
        if ($value === null) {
            return PDO::PARAM_NULL;
        }

        return PDO::PARAM_STR;
    }
}
