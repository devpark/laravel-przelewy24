<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Requests;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Factories\ActionFactory;
use Devpark\Transfers24\Factories\TestResponseFactory;
use Devpark\Transfers24\Factories\TestTranslatorFactory;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\TestConnection;

class CheckCredentialsRequest
{
    use RequestCredentialsKeeperTrait;

    /**
     * @var TestTranslatorFactory
     */
    private $test_translator_factory;

    /**
     * @var ActionFactory
     */
    private $action_factory;

    /**
     * @var TestResponseFactory
     */
    private $test_response_factory;

    public function __construct(
        TestTranslatorFactory $test_translator_factory,
        Credentials $credentials_keeper,
        ActionFactory $action_factory,
        TestResponseFactory $test_response_factory
    ) {
        $this->credentials_keeper = $credentials_keeper;
        $this->test_translator_factory = $test_translator_factory;
        $this->action_factory = $action_factory;
        $this->test_response_factory = $test_response_factory;
    }

    /**
     * @return TestConnection|InvalidResponse
     */
    public function execute():IResponse
    {
        $translator = $this->test_translator_factory->create($this->credentials_keeper);
        $action = $this->action_factory->create($this->test_response_factory, $translator);

        return $action->execute();
    }
}
