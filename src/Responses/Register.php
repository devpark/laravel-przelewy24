<?php

namespace Devpark\Transfers24\Responses;

use Devpark\Transfers24\Contracts\IResponse;

class Register extends Response implements IResponse
{
    /**
     * Get Token for registered payment.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->decoded_body->getToken();
    }
}
