<?php

namespace Devpark\Transfers24\Services\Gateways;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Factories\HttpResponseFactory;
use Devpark\Transfers24\Responses\Http\Response;
use GuzzleHttp\Client;
use Illuminate\Config\Repository as Config;

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
     * @var ClientFactory
     */
    private $client_factory;

    /**
     * Object constructor. Set initial parameters.
     *
     * @param Config $config
     */
    public function __construct(Config $config, HttpResponseFactory $http_response_factory, ClientFactory $client_factory)
    {
        $this->config = $config;
        $this->http_response_factory = $http_response_factory;
        $this->client_factory = $client_factory;

        $pos_id = $config->get('transfers24.pos_id');
        $report_key = $config->get('transfers24.report_key');
        $sandbox = $config->get('transfers24.test_server');

        $this->configure($sandbox, $pos_id, $report_key);
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
            header('Location:'.$this->hostLive.'trnRequest/'.$token);
            exit();
        } else {
            return $this->hostLive.'trnRequest/'.$token;
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
        $options = $this->buildRequestOptions($method, $form);

        $response = $this->client->request($method, $uri, $options);

        return $this->http_response_factory->create($form, $response);
    }

    /**
     * @throws NoEnvironmentChosenException
     */
    public function configureGateway(Credentials $credentials): void
    {
        if ($this->config->get('transfers24.credentials-scope')) {
            $this->configure(
                $credentials->isTestMode(),
                $credentials->getPosId(),
                $credentials->getReportKey()
            );
        }
    }

    /**
     * @param Config $config
     */
    private function configure(bool $sandbox, $pos_id, $report_key): void
    {
        $this->testMode = $sandbox;

        if ($this->testMode) {
            $this->hostLive = $this->hostSandbox;
        }

        $this->init($pos_id, $report_key);
    }

    protected function init($username, $password): void
    {
        $this->username = $username;
        $this->password = $password;
        $host = $this->getHost();
        $api_path = 'api/v1/';
        $this->client = $this->client_factory->create($host.$api_path);
    }

    private function isCommand(string $method):bool
    {
        return $method !== 'GET';
    }

    /**
     * @param string $method
     * @param Form $form
     * @return array[]
     */
    private function buildRequestOptions(string $method, Form $form): array
    {
        $options = [
            'auth' => [
                $this->username,
                $this->password,
            ],
        ];
        if ($this->isCommand($method)) {
            $form_params = $form->toArray();
            $options['form_params'] = $form_params;
        }

        return $options;
    }
}
