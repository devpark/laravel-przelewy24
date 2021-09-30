<?php
declare(strict_types=1);

namespace Tests\Feature\Requests\PaymentNotificationRequest;

use Devpark\Transfers24\Contracts\Refund;
use Devpark\Transfers24\Contracts\PaymentNotification;
use Devpark\Transfers24\Models\RefundQuery;
use Devpark\Transfers24\Services\Amount;
use Devpark\Transfers24\Services\Gateways\ClientFactory;
use GuzzleHttp\Client;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\UrlGenerator;
use Mockery as m;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;
use Ramsey\Uuid\UuidFactory;

trait PaymentNotificationRequestTrait
{

    protected function makePaymentNotification(): PaymentNotification
    {
        return new class implements PaymentNotification,Arrayable {

            public $merchantId =  0;
            public $posId =  0;
            public $sessionId =  "string";
            public $amount =  0;
            public $originAmount =  0;
            public $currency =  "PLN";
            public $orderId =  0;
            public $methodId =  0;
            public $statement =  "string";
            public $sign =  "string";

            public function toArray():array{

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
