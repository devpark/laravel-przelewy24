<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Responses;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Exceptions\ResponseException;
use Devpark\Transfers24\Exceptions\TestConnectionException;
use Devpark\Transfers24\Services\Handlers\Transfers24 as HandlerTransfers24;

class InvalidResponse implements IResponse
{
    /**
     * @var \Throwable
     */
    protected $throwable;

    public function __construct(\Throwable $throwable)
    {
        $this->throwable = $throwable;
    }

    /**
     * Get status response from transfers24.
     *
     * @return bool
     */
    public function isSuccess()
    {
        return false;
    }

    /**
     * Get Error Code back from transfers24.
     *
     * @return int
     */
    public function getErrorCode()
    {
        return $this->throwable->getCode();
    }

    /**
     * Get pairs field, error description.
     *
     * @return array
     */
    public function getErrorDescription()
    {
        return ['message' => $this->throwable->getMessage()];
    }

    /**
     * Get all parameters send with request.
     *
     * @return array
     * @throws ResponseException
     */
    public function getRequestParameters()
    {
        throw new ResponseException();
    }

    /**
     * Get Session number of payment.
     *
     * @return string
     * @throws ResponseException
     */
    public function getSessionId()
    {
        throw new ResponseException();
    }
}
