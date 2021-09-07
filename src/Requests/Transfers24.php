<?php

namespace Devpark\Transfers24\Requests;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Exceptions\NoEnvironmentChosenException;
use Devpark\Transfers24\Exceptions\RequestExecutionException;
use Devpark\Transfers24\Factories\ActionFactory;
use Devpark\Transfers24\Factories\HandlerFactory;
use Devpark\Transfers24\Factories\ReceiveResponseFactory;
use Devpark\Transfers24\Factories\ReceiveTranslatorFactory;
use Devpark\Transfers24\Factories\RegisterTranslatorFactory;
use Devpark\Transfers24\Factories\RegisterResponseFactory;
use Devpark\Transfers24\Factories\RunnerFactory;
use Devpark\Transfers24\Responses\Verify;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\UrlGenerator as Url;
use Illuminate\Foundation\Application;
use Devpark\Transfers24\Language;
use Devpark\Transfers24\Country;
use Devpark\Transfers24\Currency;
use Devpark\Transfers24\Channel;
use Devpark\Transfers24\Services\Amount;
use Devpark\Transfers24\Responses\Register as RegisterResponse;
use Devpark\Transfers24\Services\Handlers\Transfers24 as HandlersTransfers24;
use Devpark\Transfers24\Exceptions\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

/**
 * Class Transfers24.
 */
class Transfers24
{
    use RequestCredentialsKeeperTrait;
    /**
     * default quantity.
     */
    const DEFAULT_ARTICLE_QUANTITY = 1;

    /**
     * default empty description.
     */
    const DEFAULT_ARTICLE_DESCRIPTION = '';

    /**
     * default empty article number.
     */
    const NO_ARTICLE_NUMBER = '';

    /**
     * default empty article price.
     */
    const NO_PRICE_VALUE = '';

    /**
     * @var Container
     */
    protected $app;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Url
     */
    protected $url;

    /**
     * @var int|null
     */
    protected $amount = null;

    /**
     * @var string
     */
    protected $currency = Currency::PLN;

    /**
     * @var string|null
     */
    protected $customer_email = null;

    /**
     * @var string
     */
    protected $description = 'Online Payment';

    /**
     * @var string.
     */
    protected $country = Country::POLAND;

    /**
     * @var string.
     */
    protected $url_return;

    /**
     * @var string.
     */
    protected $url_status;

    /**
     * @var string|null
     */
    protected $article_name = null;

    /**
     * @var string.
     */
    protected $article_quantity = self::DEFAULT_ARTICLE_QUANTITY;

    /**
     * @var int
     */
    protected $article_price = null;

    /**
     * @var string|null
     */
    protected $article_description = null;

    /**
     * @var string|null
     */
    protected $client_name = null;

    /**
     * @var int|null
     */
    protected $client_phone = null;

    /**
     * @var string|null
     */
    protected $address = null;

    /**
     * @var string|null
     */
    protected $zip_code = null;

    /**
     * @var string|null
     */
    protected $city = null;

    /**
     * @var string
     */
    protected $language = Language::POLISH;

    /**
     * @var int|null
     */
    protected $channel = null;

    /**
     * @var int|null
     */
    protected $shipping_cost = null;

    /**
     * @var null|string
     */
    protected $transfer_label = null;

    /**
     * @var int|null
     */
    protected $article_number = null;

    /**
     * @var array|null;
     */
    protected $additional_articles = [];

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var RegisterResponse
     */
    protected $response;

    /**
     * Id of transaction.
     *
     * @var string
     */
    protected $transaction_id;
    /**
     * @var ActionFactory
     */
    protected $action_factory;
    /**
     * @var RegisterTranslatorFactory
     */
    protected $translator_factory;
    /**
     * @var RegisterResponseFactory
     */
    protected $response_factory;
    /**
     * @var RunnerFactory
     */
    private $runner_factory;
    /**
     * @var ReceiveTranslatorFactory
     */
    private $receive_translator_factory;
    /**
     * @var ReceiveResponseFactory
     */
    private $receive_response_factory;

    /**
     * Transfers24 constructor.
     *
     * @param HandlersTransfers24 $transfers24
     * @param RegisterResponse $response
     * @param Container $app
     *
     * @param Credentials $credentials_keeper
     * @param Action $action_factory
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(
        Config                    $config,
        Url                       $url,
        Credentials               $credentials_keeper,
        ActionFactory             $action_factory,
        RunnerFactory             $runner_factory,
        RegisterTranslatorFactory $translator_factory,
        RegisterResponseFactory   $response_factory,
        ReceiveTranslatorFactory  $receive_translator_factory,
        ReceiveResponseFactory    $receive_response_factory
    ) {
        $this->credentials_keeper = $credentials_keeper;
        $this->config = $config;
        $this->url = $url;

        $this->setDefaultUrls();
        $this->action_factory = $action_factory;
        $this->translator_factory = $translator_factory;
        $this->response_factory = $response_factory;
        $this->runner_factory = $runner_factory;
        $this->receive_translator_factory = $receive_translator_factory;
        $this->receive_response_factory = $receive_response_factory;
    }

    /**
     * Filter empty and no valid string.
     *
     * @param $string
     *
     * @return bool
     */
    public function filterString($string)
    {
        return (! empty($string) && is_string($string));
    }

    /**
     * Filter empty and no valid number.
     *
     * @param $number
     *
     * @return bool
     */
    public function filterNumber($number)
    {
        return (! empty($number) && is_numeric($number));
    }

    /**
     * Set description payment.
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        if ($this->filterString($description)) {
            $this->description = $description;
        }

        return $this;
    }

    /**
     * Get value of field.
     *
     * @param $label
     *
     * @return mixed|null
     */
    public function getField($label)
    {
        return isset($this->$label) ? $this->$label : null;
    }

    /**
     * Set customer email.
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        if ($this->filterString($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->customer_email = $email;
        }

        return $this;
    }

    /**
     * Set amount and currency.
     *
     * @param $amount
     *
     * @param $currency
     *
     * @return $this
     */
    public function setAmount($amount, $currency = Currency::PLN)
    {
        $this->currency = Currency::get($currency);

        $this->amount = Amount::get($amount);

        if (empty($this->article_price)) {
            $this->article_price = $this->amount;
        }

        return $this;
    }

    /**
     * Set Country Symbol.
     *
     * @param $country
     *
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = Country::get($country);

        return $this;
    }

    /**
     * Set callback url to application.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setUrlReturn($url)
    {
        if ($this->filterString($url) && filter_var($url, FILTER_VALIDATE_URL)) {
            $this->url_return = $url;
        }

        return $this;
    }

    /**
     * Set callback url to receive status message from payment service.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setUrlStatus($url)
    {
        if ($this->filterString($url) && filter_var($url, FILTER_VALIDATE_URL)) {
            $this->url_status = $url;
        }

        return $this;
    }

    /**
     * Set sale article name, price, quantity.
     *
     * @param string $article_name
     * @param float $article_price
     * @param int $article_quantity
     *
     * @return $this
     */
    public function setArticle(
        $article_name,
        $article_price = self::NO_PRICE_VALUE,
        $article_quantity = self::DEFAULT_ARTICLE_QUANTITY
    ) {
        if ($this->filterString($article_name)) {
            $this->article_name = $article_name;
        }

        if (! empty($article_price) && (is_string($article_price) || is_numeric($article_price))) {
            $this->article_price = Amount::get($article_price);
        }

        if ($this->filterNumber($article_quantity)) {
            $this->article_quantity = (int) $article_quantity;
        }

        return $this;
    }

    /**
     * Set sale article description.
     *
     * @param string $article_description
     *
     * @return $this
     */
    public function setArticleDescription($article_description)
    {
        if ($this->filterString($article_description)) {
            $this->article_description = $article_description;
        }

        return $this;
    }

    /**
     * Set client Name.
     *
     * @param string $client_name
     *
     * @return $this
     */
    public function setClientName($client_name)
    {
        if ($this->filterString($client_name)) {
            $this->client_name = $client_name;
        }

        return $this;
    }

    /**
     * Set client phone.
     *
     * @param string $client_phone
     *
     * @return $this
     */
    public function setClientPhone($client_phone)
    {
        if ($this->filterString($client_phone)) {
            $this->client_phone = $client_phone;
        }

        return $this;
    }

    /**
     * Set client address.
     *
     * @param string $address
     *
     * @return $this
     */
    public function setAddress($address)
    {
        if ($this->filterString($address)) {
            $this->address = $address;
        }

        return $this;
    }

    /**
     * Set client zip code.
     *
     * @param string $zip_code
     *
     * @return $this
     */
    public function setZipCode($zip_code)
    {
        if ($this->filterString($zip_code)) {
            $this->zip_code = $zip_code;
        }

        return $this;
    }

    /**
     * Set client city.
     *
     * @param string $city
     *
     * @return $this
     */
    public function setCity($city)
    {
        if ($this->filterString($city)) {
            $this->city = $city;
        }

        return $this;
    }

    /**
     * Set language interface.
     *
     * @param $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = Language::get($language);

        return $this;
    }

    /**
     * Set payment channel.
     *
     * @param int $channel
     *
     * @return $this
     */
    public function setChannel($channel)
    {
        $this->channel = Channel::get($channel);

        return $this;
    }

    /**
     * Set Shipping price.
     *
     * @param $shipping_cost
     *
     * @return $this
     */
    public function setShipping($shipping_cost)
    {
        if ($this->filterNumber($shipping_cost)) {
            $this->shipping_cost = (int) $shipping_cost;
        }

        return $this;
    }

    /**
     * Set transfer label.
     *
     * @param string $transfer_label
     *
     * @return $this
     */
    public function setTransferLabel($transfer_label)
    {
        if ($this->filterString($transfer_label)) {
            $this->transfer_label = $transfer_label;
        }

        return $this;
    }

    /**
     * Set sale article number.
     *
     * @param int $article_number
     *
     * @return $this
     */
    public function setArticleNumber($article_number)
    {
        if ($this->filterString($article_number)) {
            $this->article_number = $article_number;
        }

        return $this;
    }

    /**
     * Add next sale article.
     *
     * @param string $name
     * @param float $price
     * @param int $quantity
     * @param string $number
     * @param string $description
     *
     * @return $this
     */
    public function setNextArticle(
        $name,
        $price,
        $quantity = self::DEFAULT_ARTICLE_QUANTITY,
        $number = self::NO_ARTICLE_NUMBER,
        $description = self::DEFAULT_ARTICLE_DESCRIPTION
    ) {
        if ($this->filterString($name)
            && ! empty($price)
            && (is_numeric($price) || is_string($price))
        ) {
            $this->additional_articles[] = [
                'name' => $name,
                'description' => $description,
                'quantity' => $quantity,
                'price' => Amount::get($price),
                'number' => $number,
            ];
        }

        return $this;
    }



    /**
     * Register payment in payment system.
     *
     * @return RegisterResponse|IResponse
     * @throws RequestException
     */
    public function init()
    {
        if (empty($this->customer_email)
            || empty($this->amount)
            || empty($this->article_name)
        ) {
            throw new RequestException('Empty email or amount');
        }

        $translator = $this->translator_factory->create($this, $this->credentials_keeper);
        $action = $this->action_factory->create($this->response_factory, $translator);
        return $action->execute();
    }

    /**
     * Set default return url and status url.
     *
     * @return void
     */
    public function setDefaultUrls()
    {
        $this->url_return = $this->getUrl($this->config->get('transfers24.url_return'));
        $this->url_status = $this->getUrl($this->config->get('transfers24.url_status'));
    }

    /**
     * Get url.
     *
     * @param string $url
     *
     * @return string
     */
    protected function getUrl($url)
    {
        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        return $this->url->to($url);
    }

    /**
     * Return url to register Payment or redirect to transfers24.
     *
     * @param $token
     * @param bool $redirect
     *
     * @return string
     * @throws RequestExecutionException
     */
    public function execute($token, $redirect = false)
    {
        if (! is_string($token) || ! is_bool($redirect)) {
            throw new RequestExecutionException('Empty or not valid Token');
        }

        $runner = $this->runner_factory->create($this->credentials_keeper);
        return $runner->execute($token, $redirect);
    }

    /**
     * Verify payment in payment system.
     *
     * @param Request $request
     *
     * @return Verify
     */
    public function receive(Request $request)
    {

        $translator = $this->receive_translator_factory->create($request->all(), $this->credentials_keeper);
        $action = $this->action_factory->create($this->receive_response_factory, $translator);
        return $action->execute();
    }

    /**
     * @return int|null
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getCustomerEmail(): ?string
    {
        return $this->customer_email;
    }

    /**
     * @return string|null
     */
    public function getClientName(): ?string
    {
        return $this->client_name;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @return string|null
     */
    public function getZipCode(): ?string
    {
        return $this->zip_code;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return int|null
     */
    public function getClientPhone(): ?int
    {
        return $this->client_phone;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getUrlReturn(): string
    {
        return $this->url_return;
    }

    /**
     * @return string
     */
    public function getUrlStatus(): string
    {
        return $this->url_status;
    }

    /**
     * @return int|null
     */
    public function getChannel(): ?int
    {
        return $this->channel;
    }

    /**
     * @return string|null
     */
    public function getArticleName(): ?string
    {
        return $this->article_name;
    }

    /**
     * @return string|null
     */
    public function getArticleDescription(): ?string
    {
        return $this->article_description;
    }

    /**
     * @return string
     */
    public function getArticleQuantity()
    {
        return $this->article_quantity;
    }

    /**
     * @return int
     */
    public function getArticlePrice(): ?int
    {
        return $this->article_price;
    }

    /**
     * @return int|null
     */
    public function getArticleNumber(): ?int
    {
        return $this->article_number;
    }

    /**
     * @return int|null
     */
    public function getShippingCost(): ?int
    {
        return $this->shipping_cost;
    }

    /**
     * @return array|null
     */
    public function getAdditionalArticles(): ?array
    {
        return $this->additional_articles;
    }
}
