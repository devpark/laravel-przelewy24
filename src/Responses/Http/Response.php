<?php

namespace Devpark\Transfers24\Responses\Http;

use Devpark\Transfers24\Contracts\Form;

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
     * $var Form.
     */
    protected $form;

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
     * @param array $form
     */
    public function addFormParams(Form $form)
    {
        $this->form = $form;
    }

    /**
     * Get Http Request params.
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }
}
