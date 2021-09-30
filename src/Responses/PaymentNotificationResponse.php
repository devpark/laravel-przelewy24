<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Responses;

use Devpark\Transfers24\Contracts\PaymentNotification;
use Illuminate\Support\Arr;

class PaymentNotificationResponse
{
    /**
     * @var array
     */
    private $notification;

    public function __construct(array $receive_data)
    {
        $this->notification = $receive_data;
    }

    /**
     * @return PaymentNotification
     */
    public function getNotification():PaymentNotification
    {
        return $this->convert($this->notification);
    }

    private function convert(array $data):PaymentNotification
    {
        return new class($data) implements PaymentNotification {
            /**
             * @var array
             */
            protected $data;

            public function __construct(array $data)
            {
                $this->data = $data;
            }

            public function __get(string $name)
            {
                return Arr::get($this->data, $name);
            }
        };
    }

    public function getResponse():string
    {
        return 'ok';
    }
}
