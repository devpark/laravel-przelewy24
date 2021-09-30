<?php

declare(strict_types=1);

namespace Tests\Feature\Requests\PaymentNotificationRequest;

use Devpark\Transfers24\Contracts\PaymentNotification;
use Illuminate\Contracts\Support\Arrayable;

trait PaymentNotificationRequestTrait
{
    protected function makePaymentNotification(): PaymentNotification
    {
        return new class implements PaymentNotification, Arrayable {
            public $merchantId = 0;

            public $posId = 0;

            public $sessionId = 'string';

            public $amount = 0;

            public $originAmount = 0;

            public $currency = 'PLN';

            public $orderId = 0;

            public $methodId = 0;

            public $statement = 'string';

            public $sign = 'string';

            public function toArray():array
            {
                return [
                    'merchantId' => $this->merchantId,
                    'posId' => $this->posId,
                    'sessionId' => $this->sessionId,
                    'amount' => $this->amount,
                    'originAmount' => $this->originAmount,
                    'currency' => $this->currency,
                    'orderId' => $this->orderId,
                    'methodId' => $this->methodId,
                    'statement' => $this->statement,
                    'sign' => $this->sign,
                ];
            }
        };
    }
}
