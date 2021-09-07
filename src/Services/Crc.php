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

    /**
     * Check Sum Control incoming data with status payment.
     *
     * @param array $post_data
     *
     * @return bool
     */
    public function checkSum(array $post_data)
    {
        $params = ['p24_session_id', 'p24_order_id', 'p24_amount', 'p24_currency'];

        $crc = $this->sum($params, $post_data);

        return $crc == $post_data['p24_sign'];
    }
}
