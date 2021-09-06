<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Forms;

use Devpark\Transfers24\Contracts\Form;

class RegisterForm implements Form
{

    private $postData = [];

    public function toArray():array{

//        $this->addValue('p24_merchant_id', $this->merchantId);
//        $this->addValue('p24_pos_id', $this->posId);
//        $this->addValue('p24_api_version', );

        return $this->postData;
    }

    public function getOrderId(): string
    {
        // TODO: Implement getOrderId() method.
    }

    public function getSessionId(): string
    {
        // TODO: Implement getSessionId() method.
    }

    public function getUri(): string
    {
        // TODO: Implement getUri() method.
    }

    public function getMethod(): string
    {
        // TODO: Implement getMethod() method.
    }

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
        $this->postData[$name] = $value;
    }
}