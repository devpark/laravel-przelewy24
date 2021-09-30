<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Actions;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\ResponseFactory;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Exceptions\EmptyCredentialsException;
use Devpark\Transfers24\Exceptions\NoEnvironmentChosenException;
use Devpark\Transfers24\Factories\RegisterResponseFactory;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Services\Gateways\Transfers24;
use Psr\Log\LoggerInterface;

class Action
{
    /**
     * @var RegisterResponseFactory
     */
    protected $response_factory;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var Transfers24
     */
    protected $gateway;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(Transfers24 $gateway, LoggerInterface $logger)
    {
        $this->gateway = $gateway;
        $this->logger = $logger;
    }

    public function init(ResponseFactory $response_factory, Translator $translator):Action
    {
        $this->response_factory = $response_factory;
        $this->translator = $translator;

        return $this;
    }

    public function execute():IResponse
    {
        try {
            $this->translator->configure();
            $form = $this->translator->translate();

            $this->gateway->configureGateway($this->translator->getCredentials());
            $gateway_response = $this->gateway->callTransfers24($form);

            return $this->response_factory->create($gateway_response);
        } catch (EmptyCredentialsException $exception) {
            $this->logger->error($exception->getMessage());

            return new InvalidResponse($exception);
        } catch (NoEnvironmentChosenException $exception) {
            $this->logger->error($exception->getMessage());

            return new InvalidResponse($exception);
        } catch (\Throwable $exception) {
            return new InvalidResponse($exception);
        }
    }
}
