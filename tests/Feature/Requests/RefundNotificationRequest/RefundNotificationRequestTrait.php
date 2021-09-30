<?php

declare(strict_types=1);

namespace Tests\Feature\Requests\RefundNotificationRequest;

use Devpark\Transfers24\Contracts\RefundNotification;
use Illuminate\Contracts\Support\Arrayable;

trait RefundNotificationRequestTrait
{
    protected function makeRefundNotification(): RefundNotification
    {
        return new class implements RefundNotification, Arrayable {
            public $orderId = 0;

            public $sessionId = 'string';

            public $merchantId = 0;

            public $requestId = 'string';

            public $refundsUuid = 'string';

            public $amount = 0;

            public $currency = 'PLN';

            public $timestamp = 0;

            public $status = 0;

            public $sign = 'string';

            public function toArray():array
            {
                return [
                    'orderId' => $this->orderId,
                    'sessionId' => $this->sessionId,
                    'merchantId' => $this->merchantId,
                    'requestId' => $this->requestId,
                    'refundsUuid' => $this->refundsUuid,
                    'amount' => $this->amount,
                    'currency' => $this->currency,
                    'timestamp' => $this->timestamp,
                    'status' => $this->status,
                    'sign' => $this->sign,
                ];
            }
        };
    }
}
