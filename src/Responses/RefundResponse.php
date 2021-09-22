<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Responses;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\Refund;
use Devpark\Transfers24\Exceptions\TestConnectionException;

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
        return $this->decoded_body->getData();
    }
}
