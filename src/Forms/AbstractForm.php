<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Forms;

use Devpark\Transfers24\Contracts\Form;

abstract class AbstractForm
{

    protected $postData = [];

    /**
     * @var string
     */
    protected $session_id;
    /**
     * @var string
     */
    protected $order_id;

    public function toArray():array{
        return $this->postData;
    }

    public function getOrderId(): string
    {
        return $this->order_id;
    }

    public function getSessionId(): string
    {
        return $this->session_id;
    }

    abstract public function getUri(): string;

    abstract public function getMethod(): string;

    /**
     * Add value do post request.
     *
     * @param string $name Argument name
     * @param mixed $value Argument value
     *
     * @return void
     */
    public function addValue(string $name, $value)
    {
        if (isset($value) && ! empty($value)) {
            $this->postData[$name] = $value;
        }
    }

    /**
     * @param string $session_id
     */
    public function setSessionId(string $session_id): void
    {
        $this->session_id = $session_id;
    }

    /**
     * @param string $order_id
     */
    public function setOrderId(string $order_id): void
    {
        $this->order_id = $order_id;
    }
}
