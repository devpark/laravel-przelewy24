<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Requests;

use Devpark\Transfers24\Responses\PaymentNotificationResponse;
use Illuminate\Http\Request;

class PaymentNotificationRequest
{
    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute():PaymentNotificationResponse
    {
        return new PaymentNotificationResponse($this->request->all());
    }
}
