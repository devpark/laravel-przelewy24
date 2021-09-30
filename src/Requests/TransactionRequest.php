<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Requests;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Exceptions\RequestException;
use Devpark\Transfers24\Factories\ActionFactory;
use Devpark\Transfers24\Factories\ForResponses\PaymentMethodsResponseFactory;
use Devpark\Transfers24\Factories\ForResponses\TransactionResponseFactory;
use Devpark\Transfers24\Factories\ForTranslators\PaymentMethodsTranslatorFactory;
use Devpark\Transfers24\Factories\ForTranslators\TransactionTranslatorFactory;
use Devpark\Transfers24\Language;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\PaymentMethodsResponse;
use Devpark\Transfers24\Responses\TestConnection;
use Devpark\Transfers24\Responses\TransactionResponse;

class TransactionRequest
{
    use RequestCredentialsKeeperTrait;

    /**
     * @var TransactionTranslatorFactory
     */
    private $translator_factory;
    /**
     * @var ActionFactory
     */
    private $action_factory;
    /**
     * @var TransactionResponseFactory
     */
    private $response_factory;

    /**
     * @var string
     */
    protected $session_id;

    public function __construct(
        TransactionTranslatorFactory $translator_factory, Credentials $credentials_keeper,
        ActionFactory $action_factory, TransactionResponseFactory $response_factory
    )
    {
        $this->credentials_keeper = $credentials_keeper;
        $this->translator_factory = $translator_factory;
        $this->action_factory = $action_factory;
        $this->response_factory = $response_factory;
    }

    /**
     * @return TransactionResponse|InvalidResponse
     */
    public function execute():IResponse
    {
        if (empty($this->session_id)){
            throw new RequestException('Empty session-Id');
        }

        $translator = $this->translator_factory->create($this->credentials_keeper, $this->getSessionId());
        $action = $this->action_factory->create($this->response_factory, $translator);
        return $action->execute();
    }

    /**
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->session_id;
    }

    /**
     * Set session_id interface.
     *
     * @param $session_id
     *
     * @return $this
     */
    public function setSessionId($session_id)
    {
        $this->session_id = $session_id;

        return $this;
    }

}
