<?php

namespace Devpark\Transfers24\Responses\Http;

/**
 * Class Response.
 */
class Response
{
    /**
     * $var int.
     */
    protected $status_code;

    /**
     * $var string.
     */
    protected $body;

    /**
     * $var array.
     */
    protected $form_params = [];

    /**
     * Add Http Response Code.
     *
     * @param int $status_code
     */
    public function addStatusCode($status_code)
    {
        $this->status_code = $status_code;
    }

    /**
     * Add Http Response body.
     *
     * @param string $body
     */
    public function addBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get Http Response Code.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * Get Http Response Code.
     *
     * $return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Add Http Request params.
     *
     * @param array $form_params
     */
    public function addFormParams(array $form_params)
    {
        $this->form_params = $form_params;
    }

    /**
     * Get Http Request params.
     *
     * @return array
     */
    public function getFormParams()
    {
        return $this->form_params;
    }
}
