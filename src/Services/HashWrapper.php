<?php

namespace Devpark\Transfers24\Services;

/**
 * Class Amount.
 */
class HashWrapper
{
    public function hash(array $params):string
    {
        return hash('sha384', json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
