<?php

namespace Devpark\Transfers24\Services;

class DecodedBody
{
    private $token;

    private $status_code;

    /**
     * @var string
     */
    private $error_message;

    private $data;

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
     * @param string $error_message
     */
    public function setErrorMessage($error_message): void
    {
        $this->error_message = $error_message;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }
}
