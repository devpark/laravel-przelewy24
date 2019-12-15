<?php

namespace Devpark\Transfers24\Responses;

use Devpark\Transfers24\Contracts\IResponse;

/**
 * Class Verify.
 */
class Verify extends Response implements IResponse
{
    /**
     * Get all parameters receive with request.
     *
     * @return array
     */
    public function getReceiveParameters()
    {
        return $this->transfers24->getReceiveParameters();
    }

    /**
     * Get Transaction number received from transfers24.
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->transfers24->getOrderId();
    }
}
