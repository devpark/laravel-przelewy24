<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Responses;

use Devpark\Transfers24\Contracts\RefundNotification;
use Illuminate\Support\Arr;

class NotificationResponse
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
     * @return RefundNotification
     */
    public function getNotification():RefundNotification
    {
        return $this->convert($this->notification);
    }

    private function convert(array $data):RefundNotification
    {
        return new class($data) implements RefundNotification {
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
