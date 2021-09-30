<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Requests;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Factories\ActionFactory;
use Devpark\Transfers24\Factories\ForResponses\PaymentMethodsResponseFactory;
use Devpark\Transfers24\Factories\ForResponses\RefundResponseFactory;
use Devpark\Transfers24\Factories\ForTranslators\PaymentMethodsTranslatorFactory;
use Devpark\Transfers24\Factories\ForTranslators\RefundNotificationTranslatorFactory;
use Devpark\Transfers24\Factories\ForTranslators\RefundTranslatorFactory;
use Devpark\Transfers24\Language;
use Devpark\Transfers24\Models\RefundQuery;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\PaymentMethodsResponse;
use Devpark\Transfers24\Responses\NotificationResponse;
use Devpark\Transfers24\Responses\RefundResponse;
use Devpark\Transfers24\Responses\TestConnection;
use Devpark\Transfers24\Services\Amount;

class RefundNotificationRequest
{
    public function execute(array $notification):NotificationResponse
    {
        return new NotificationResponse($notification);
    }

}