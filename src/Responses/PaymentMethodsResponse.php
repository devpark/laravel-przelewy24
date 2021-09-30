<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Responses;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\PaymentMethod;
use Devpark\Transfers24\Contracts\PaymentMethodHours;
use Devpark\Transfers24\Exceptions\TestConnectionException;
use Illuminate\Support\Arr;

class PaymentMethodsResponse extends Response implements IResponse
{
    /**
     * Get Session number of payment.
     *
     * @return string
     * @throws TestConnectionException
     */
    public function getSessionId()
    {
        throw new TestConnectionException();
    }

    /**
     * @return PaymentMethod[]
     */
    public function getPaymentMethods():array
    {
        return array_map([$this, 'convert'], $this->decoded_body->getData());
    }

    private function convert(array $data):PaymentMethod
    {
        return new class($data) implements PaymentMethod {
            /**
             * @var array
             */
            protected $data;

            public function __construct(array $data)
            {
                $this->data = $data;
            }

            public function __get(string $name){
                if ($name == 'availabilityHours'){
                    $payment_method_hours = Arr::get($this->data, $name, []);
                    return $this->convertAvailabilityHours($payment_method_hours);
                }
                return Arr::get($this->data, $name);
            }

            private function convertAvailabilityHours(array $data):PaymentMethodHours
            {
                return new class($data) implements PaymentMethodHours {
                    /**
                     * @var array
                     */
                    protected $data;

                    public function __construct(array $data)
                    {
                        $this->data = $data;
                    }

                    public function __get(string $name){
                        return Arr::get($this->data, $name);
                    }
                };
            }
        };
    }

}
