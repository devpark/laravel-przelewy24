<?php

namespace Devpark\Transfers24\Services;

/**
 * Class Amount.
 */
class Crc
{
    /**
     * @var string
     */
    private $salt;

    public function sum(array $params, array $array_values):string
    {
        $form_params = [];

        foreach ($params as $param) {
            if (! isset($array_values[$param])) {
                return '';
            }
            $form_params[] = $array_values[$param];
        }
        if (!empty($this->salt)){
            $form_params[] = $this->salt;
        }

        $concat = implode('|', $form_params);
        $crc = md5($concat);

        return $crc;
    }

    public function setSalt(string $salt)
    {
        $this->salt = $salt;
    }
}
