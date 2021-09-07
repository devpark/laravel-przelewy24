<?php

namespace Devpark\Transfers24\Services\Gateways;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Factories\HttpResponseFactory;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Services\Crc;
use GuzzleHttp\Client;
use Devpark\Transfers24\Responses\Http\Response;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Container\Container;

/**
 * Class Transfers24.
 */
class Transfers24
{
    /**
     * Live system URL address.
     *
     * @var string
     */
    protected $hostLive = 'https://secure.przelewy24.pl/';

    /**
     * Sandbox system URL address.
     *
     * @var string
     */
    protected $hostSandbox = 'https://sandbox.przelewy24.pl/';

    /**
     * Use Live (false) or Sandbox (true) environment.
     *
     * @var bool
     */
    protected $testMode = false;

    /**
     * Merchant posId.
     *
     * @var int
     */
    protected $posId;


    /**
     * Salt to create a control sum (from P24 panel).
     *
     * @var string
     */
    protected $salt;

    /**
     * client curl.
     *
     * @var Client
     */
    protected $client;

    /**
     * @var HttpResponseFactory response
     */
    protected $http_response_factory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Container
     */
    private $app;

    /**
     * Object constructor. Set initial parameters.
     *
     * @param Config $config
     */
    public function __construct(Config $config, HttpResponseFactory $http_response_factory, Container $app)
    {
        $this->config = $config;
        $this->http_response_factory = $http_response_factory;
        $this->app = $app;

        $sandbox = $config->get('transfers24.test_server');

        $this->configure(
            $sandbox
        );
    }

    /**
     * Returns host URL.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->hostLive;
    }

    /**
     * Redirects or returns URL to a P24 payment screen.
     *
     * @param string $token
     * @param bool $redirect If set to true redirects to P24 payment screen. If set to false
     * function returns URL to redirect to P24 payment screen
     *
     * @return string URL to P24 payment screen
     */
    public function trnRequest($token, $redirect = true)
    {
        if ($redirect) {
            header('Location:' . $this->hostLive . 'trnRequest/' . $token);
            exit();
        } else {
            return $this->hostLive . 'trnRequest/' . $token;
        }
    }

    /**
     * Function connect to P24 system.
     *
     * @param \Devpark\Transfers24\Services\Handlers\Transfers24 $handler
     * @return Response
     */
    public function callTransfers24(Form $form): Response
    {
        $uri = $form->getUri();
        $method = $form->getMethod();
        $form_params = $form->toArray();

        $response = $this->client->request($method, $uri,
            ['form_params' => $form_params]
        );

        return $this->http_response_factory->create($form, $response);
    }


    /**
     * @throws NoEnvironmentChosenException
     */
    public function configureGateway(Credentials $credentials): void
    {
        if ($this->config->get('transfers24.credentials-scope')) {
            $this->configure(
                $credentials->isTestMode()
            );
        }
    }

    /**
     * @param Config $config
     */
    private function configure(bool $sandbox): void
    {

        $this->testMode = $sandbox;

        if ($this->testMode) {
            $this->hostLive = $this->hostSandbox;
        }

        $this->init();

    }

    protected function init(): void
    {
        $this->client = $this->app->make(Client::class, [
            'base_uri' => $this->getHost()
        ]);
    }
}
