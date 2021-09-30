<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Contracts;

interface IResponse
{
    /**
     * Get status response from transfers24.
     *
     * @return bool
     */
    public function isSuccess();

    /**
     * Get Error Code back from transfers24.
     *
     * @return int
     */
    public function getErrorCode();

    /**
     * Get pairs field, error description.
     *
     * @return array
     */
    public function getErrorDescription();

    /**
     * Get all parameters send with request.
     *
     * @return array
     */
    public function getRequestParameters();

    /**
     * Get Session number of payment.
     *
     * @return string
     */
    public function getSessionId();
}
