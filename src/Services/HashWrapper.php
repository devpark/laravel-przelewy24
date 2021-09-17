<?php

namespace Devpark\Transfers24\Services;

/**
 * Class Amount.
 */
class HashWrapper
{
    public function hash(array $params):string
    {
        if (isset($params['merchantId'])){
            $params['merchantId'] = (int)$params['merchantId'];
        }
        if (isset($params['orderId'])){
            $params['orderId'] = (int)$params['orderId'];
        }
        if (isset($params['amount'])){
            $params['amount'] = (int)$params['amount'];
        }
        return hash('sha384', json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
