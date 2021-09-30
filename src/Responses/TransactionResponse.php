<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Responses;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\Transaction;
use Devpark\Transfers24\Exceptions\TestConnectionException;
use Illuminate\Support\Arr;

class TransactionResponse extends Response implements IResponse
{
    public function getTransaction():Transaction
    {
        return $this->convert($this->decoded_body->getData());
    }

    private function convert(array $data):Transaction
    {
        return new class($data) implements Transaction {
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
