<?php

namespace Devpark\Transfers24\Services\Gateways;

use Devpark\Transfers24\Factories\HttpResponseFactory;
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
     * Merchant Id.
     *
     * @var int
     */
    protected $merchantId;

    /**
     * Salt to create a control sum (from P24 panel).
     *
     * @var string
     */
    protected $salt;

    /**
     * Array of POST data.
     *
     * @var array
     */
    protected $postData = [];

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
     * $var array.
     */
    protected $crc_parts = [];


    /**
     * @var Container
     */
    private $app;
    /**
     * @var Crc
     */
    private $crc;

    /**
     * Object constructor. Set initial parameters.
     *
     * @param Config $config
     */
    public function __construct(Config $config, HttpResponseFactory $http_response_factory, Container $app, Crc $crc)
    {
        $this->config = $config;
        $this->http_response_factory = $http_response_factory;
        $this->app = $app;
        $this->crc = $crc;


        $pos_id = $this->config->get('transfers24.pos_id');
        $merchant_id = $this->config->get('transfers24.merchant_id');
        $crc = $this->config->get('transfers24.crc');
        $sandbox = $config->get('transfers24.test_server');

        $this->addValue('p24_api_version', $config->get('transfers24.version'));

        $this->configure(
            $pos_id,
            $merchant_id,
            $crc,
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
     * Add value do post request.
     *
     * @param string $name Argument name
     * @param mixed $value Argument value
     *
     * @return void
     */
    public function addValue($name, $value)
    {
        $this->postData[$name] = $value;
    }

    /**
     * Function is testing a connection with P24 server.
     *
     * @return Response
     */
    public function testConnection()
    {
        $this->calculateSign(['p24_merchant_id', 'p24_pos_id']);

        return $this->callTransfers24('testConnection');
    }

    /**
     * Prepare a transaction request.
     *
     * @param array $fields
     *
     * @return Response
     */
    public function trnRegister(array $fields)
    {
        return $this->callTransfers24('trnRegister');
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
     * Function verify received from P24 system transaction's result.
     *
     * @param array $fields
     *
     * @return Response object
     */
    public function trnVerify(array $fields)
    {
//        $this->postData += $fields;

//        $this->calculateSign(['p24_session_id', 'p24_order_id', 'p24_amount', 'p24_currency']);

        return $this->callTransfers24('trnVerify');
    }

    /**
     * Function connect to P24 system.
     *
     * @param \Devpark\Transfers24\Services\Handlers\Transfers24 $handler
     * @return Response
     */
    public function callTransfers24(\Devpark\Transfers24\Services\Handlers\Transfers24 $handler): Response
    {
//        $this->postData += $fields;

        $form = $handler->getForm();
        $this->postData += $form->toArray();

        $this->calculateSign(['p24_session_id', 'p24_merchant_id', 'p24_amount', 'p24_currency']);

        $uri = $handler->getUri();
        $method = $handler->getMethod();
        $form_params = $this->postData;

        $response = $this->client->request($method, $uri,
            ['form_params' => $form_params]
        );

        return $this->http_response_factory->create($form, $response);
    }

    /**
     * Calculated CRC sum on params.
     *
     * @param array $params
     * @param array $array_values
     *
     * @return string
     */
    protected function calculateCrcSum(array $params, array $array_values)
    {
        $this->crc->setSalt($this->salt);
        return $this->crc->sum($params, $array_values);
    }

    /**
     * Add CRC sum on params send to transfers24.
     *
     * @param array $params
     *
     * @return void
     */
    protected function calculateSign(array $params)
    {
        $crc = $this->calculateCrcSum($params, $this->postData);

        $this->addValue('p24_sign', $crc);
    }

    /**
     * Check Sum Control incoming data with status payment.
     *
     * @param array $post_data
     *
     * @return bool
     */
    public function checkSum(array $post_data)
    {
        $params = ['p24_session_id', 'p24_order_id', 'p24_amount', 'p24_currency'];

        $crc = $this->calculateCrcSum($params, $post_data);

        return $crc == $post_data['p24_sign'];
    }

    /**
     * @param Config $config
     */
    public function configure(
        $pos_id,
        $merchant_id,
        $crc,
        bool $sandbox
    ): void
    {

        $this->posId = $pos_id;
        $this->merchantId = $merchant_id;
        $this->salt = $crc;
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

        $this->addValue('p24_merchant_id', $this->merchantId);
        $this->addValue('p24_pos_id', $this->posId);
    }
}
