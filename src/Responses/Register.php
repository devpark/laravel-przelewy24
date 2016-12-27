<?php

namespace Devpark\Transfers24\Responses;

class Register extends Response
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
