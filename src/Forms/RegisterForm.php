<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Forms;

use Devpark\Transfers24\Contracts\Form;

class RegisterForm implements Form
{

    public function toArray():array{

//        $this->addValue('p24_merchant_id', $this->merchantId);
//        $this->addValue('p24_pos_id', $this->posId);
//        $this->addValue('p24_api_version', $config->get('transfers24.version'));

        return [];
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
}
