<?php

namespace Devpark\Transfers24\Responses;

use Devpark\Transfers24\Contracts\IResponse;

class Register extends Response implements IResponse
{
    /**
     * Get Token for registered payment.
     *
     * @return int
     */
    public function getToken()
    {
        return $this->transfers24->getToken();
    }
}
