<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Requests;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Factories\ActionFactory;
use Devpark\Transfers24\Factories\ForResponses\PaymentMethodsResponseFactory;
use Devpark\Transfers24\Factories\ForResponses\RefundResponseFactory;
use Devpark\Transfers24\Factories\ForTranslators\PaymentMethodsTranslatorFactory;
use Devpark\Transfers24\Factories\ForTranslators\RefundTranslatorFactory;
use Devpark\Transfers24\Language;
use Devpark\Transfers24\Models\RefundQuery;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\PaymentMethods;
use Devpark\Transfers24\Responses\RefundResponse;
use Devpark\Transfers24\Responses\TestConnection;
use Devpark\Transfers24\Services\Amount;

class RefundRequest
{
    use RequestCredentialsKeeperTrait;

    /**
     * default empty description.
     */
    const DEFAULT_ARTICLE_DESCRIPTION = '';

    /**
     * @var RefundTranslatorFactory
     */
    private $translator_factory;
    /**
     * @var ActionFactory
     */
    private $action_factory;
    /**
     * @var PaymentMethodsResponseFactory
     */
    private $response_factory;

    /**
     * @var string
     */
    protected $language = Language::POLISH;
    /**
     * @var RefundQuery[]
     */
    private $refund_inquiries = [];

    public function __construct(
        RefundTranslatorFactory $translator_factory, Credentials $credentials_keeper,
        ActionFactory $action_factory, RefundResponseFactory $response_factory
    )
    {
        $this->credentials_keeper = $credentials_keeper;
        $this->translator_factory = $translator_factory;
        $this->action_factory = $action_factory;
        $this->response_factory = $response_factory;
    }

    /**
     * @return RefundResponse|InvalidResponse
     */
    public function execute():IResponse
    {
        $translator = $this->translator_factory->create($this->credentials_keeper, $this);
        $action = $this->action_factory->create($this->response_factory, $translator);
        return $action->execute();
    }

    /**
     * Add Refund Inquiry
     *
     * @return $this
     */
    public function addRefundInquiry(int $order_id, string $session_id, float $amount, string $description = self::DEFAULT_ARTICLE_DESCRIPTION) {

        $this->refund_inquiries[] = (new RefundQuery($order_id, $session_id, Amount::get($amount), $description))->toArray();

        return $this;
    }

    /**
     * @return RefundQuery[]
     */
    public function getRefundInquiries(): array
    {
        return $this->refund_inquiries;
    }


}
