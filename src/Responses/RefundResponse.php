<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Responses;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\Refund;
use Devpark\Transfers24\Exceptions\TestConnectionException;
use Illuminate\Support\Arr;

class RefundResponse extends Response implements IResponse
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
     * @return Refund[]
     */
    public function getRefunds():array
    {
        return array_map([$this, 'convert'], $this->decoded_body->getData());
    }

    private function convert(array $data):Refund
    {
        return new class($data) implements Refund {
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
}
