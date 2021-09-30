<?php

namespace Devpark\Transfers24\Services;

/**
 * Class Amount.
 */
class Crc
{
    /**
     * @var HashWrapper
     */
    protected $hash_wrapper;

    public function __construct(HashWrapper $hash_wrapper)
    {
        $this->hash_wrapper = $hash_wrapper;
    }

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
            $form_params[$param] = $array_values[$param];
        }
        if (! empty($this->salt)) {
            $form_params += ['crc' => $this->salt];
        }

        return $this->hash_wrapper->hash($form_params);
    }

    public function setSalt(string $salt)
    {
        $this->salt = $salt;
    }
}
