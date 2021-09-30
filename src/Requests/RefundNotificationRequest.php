<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Requests;

use Devpark\Transfers24\Responses\NotificationResponse;
use Illuminate\Http\Request;

class RefundNotificationRequest
{
    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute():NotificationResponse
    {
        return new NotificationResponse($this->request->all());
    }
}
