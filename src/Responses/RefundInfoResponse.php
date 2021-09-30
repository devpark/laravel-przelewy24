<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Responses;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\RefundInfo;
use Devpark\Transfers24\Contracts\RefundInfoData;
use Illuminate\Support\Arr;

class RefundInfoResponse extends Response implements IResponse
{
    public function getRefundInfo():RefundInfoData
    {
        return $this->convert($this->decoded_body->getData());
    }

    private function convert(array $data):RefundInfoData
    {
        return new class($data) implements RefundInfoData {
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
                if ($name == 'refunds') {
                    $refunds = Arr::get($this->data, $name, []);

                    return array_map([$this, 'convertRefund'], $refunds);
                }

                return Arr::get($this->data, $name);
            }

            private function convertRefund(array $data):RefundInfo
            {
                return new class($data) implements RefundInfo {
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
        };
    }
}
