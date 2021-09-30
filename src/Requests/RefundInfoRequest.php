<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Requests;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Exceptions\RequestException;
use Devpark\Transfers24\Factories\ActionFactory;
use Devpark\Transfers24\Factories\ForResponses\RefundInfoResponseFactory;
use Devpark\Transfers24\Factories\ForTranslators\RefundInfoTranslatorFactory;
use Devpark\Transfers24\Responses\InvalidResponse;

class RefundInfoRequest
{
    use RequestCredentialsKeeperTrait;

    /**
     * @var RefundInfoTranslatorFactory
     */
    private $translator_factory;

    /**
     * @var ActionFactory
     */
    private $action_factory;

    /**
     * @var RefundInfoResponseFactory
     */
    private $response_factory;

    /**
     * @var string
     */
    protected $order_id;

    public function __construct(
        RefundInfoTranslatorFactory $translator_factory,
        Credentials $credentials_keeper,
        ActionFactory $action_factory,
        RefundInfoResponseFactory $response_factory
    ) {
        $this->credentials_keeper = $credentials_keeper;
        $this->translator_factory = $translator_factory;
        $this->action_factory = $action_factory;
        $this->response_factory = $response_factory;
    }

    /**
     * @return RefungInfoResponse|InvalidResponse
     */
    public function execute():IResponse
    {
        if (empty($this->order_id)) {
            throw new RequestException('Empty Order Id');
        }

        $translator = $this->translator_factory->create($this->credentials_keeper, $this->order_id);
        $action = $this->action_factory->create($this->response_factory, $translator);

        return $action->execute();
    }

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->order_id;
    }

    /**
     * Set session_id interface.
     *
     * @param $order_id
     *
     * @return $this
     */
    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;

        return $this;
    }
}
