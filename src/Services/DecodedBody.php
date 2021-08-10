<?php

namespace Devpark\Transfers24\Services;

class DecodedBody
{
    private $token;
    private $status_code;
    /**
     * @var array
     */
    private $error_message;

    /**
     * Get Token for registered payment.
     *
     * @return int
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }

    public function setStatusCode($segment)
    {
        $this->status_code = $segment;
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }


    /**
     * @param array $error_message
     */
    public function setErrorMessage(array $error_message): void
    {
        $this->error_message = $error_message;
    }

    /**
     * @return array
     */
    public function getErrorMessage(): array
    {
        return $this->error_message;
    }
}
