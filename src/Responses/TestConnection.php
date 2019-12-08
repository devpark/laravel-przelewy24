<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Responses;

use Devpark\Transfers24\Exceptions\TestConnectionException;

class TestConnection extends Response
{
    /**
     * Get Session number of payment.
     *
     * @return string
     */
    public function getSessionId()
    {
        throw new TestConnectionException();
    }
}
