<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Requests;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Factories\ActionFactory;
use Devpark\Transfers24\Factories\ForResponses\RegisterOfflineResponseFactory;
use Devpark\Transfers24\Factories\ForTranslators\RegisterOfflineTranslatorFactory;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\RegisterOfflineResponse;

class RegisterOfflineRequest
{
    use RequestCredentialsKeeperTrait;

    /**
     * @var RegisterOfflineTranslatorFactory
     */
    private $translator_factory;

    /**
     * @var ActionFactory
     */
    private $action_factory;

    /**
     * @var RegisterOfflineResponseFactory
     */
    private $response_factory;

    /**
     * @var string
     */
    protected $token;

    public function __construct(
        RegisterOfflineTranslatorFactory $translator_factory,
        Credentials $credentials_keeper,
        ActionFactory $action_factory,
        RegisterOfflineResponseFactory $response_factory
    ) {
        $this->credentials_keeper = $credentials_keeper;
        $this->translator_factory = $translator_factory;
        $this->action_factory = $action_factory;
        $this->response_factory = $response_factory;
    }

    /**
     * @return RegisterOfflineResponse|InvalidResponse
     */
    public function execute():IResponse
    {
        $translator = $this->translator_factory->create($this->credentials_keeper, $this->token);
        $action = $this->action_factory->create($this->response_factory, $translator);

        return $action->execute();
    }

    /**
     * Set token.
     *
     * @param $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }
}
