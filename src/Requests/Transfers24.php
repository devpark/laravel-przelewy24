<?php

namespace Devpark\Transfers24\Requests;

use Devpark\Transfers24\Exceptions\RequestExecutionException;
use Devpark\Transfers24\Responses\Verify;
use Illuminate\Config\Repository as Config;
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

/**
 * Class Transfers24.
 */
class Transfers24
{
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
     * @var Application
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
     * @var HandlersTransfers24
     */
    protected $transfers24;

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
     * Transfers24 constructor.
     *
     * @param HandlersTransfers24 $transfers24
     * @param RegisterResponse $response
     * @param Application $app
     */
    public function __construct(
        HandlersTransfers24 $transfers24,
        RegisterResponse $response,
        Application $app
    ) {
        $this->response = $response;
        $this->transfers24 = $transfers24;
        $this->app = $app;

        $this->config = $this->app->make(Config::class);
        $this->url = $this->app->make(Url::class);

        $this->setDefaultUrls();
    }

    /**
     * Filter empty and no valid string.
     *
     * @param $string
     *
     * @return bool
     */
    protected function filterString($string)
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
    protected function filterNumber($number)
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
     * add parameter to $fields.
     *
     * @param string $label
     * @param string $value
     */
    public function setField($label, $value)
    {
        if (isset($value) && ! empty($value)) {
            $this->fields[$label] = $value;
        }
    }

    /**
     * Set parameter for transfers24.
     *
     * @return array
     */
    public function setFields()
    {
        $this->setField('p24_session_id', $this->transaction_id);
        $this->setField('p24_amount', $this->amount);
        $this->setField('p24_currency', $this->currency);
        $this->setField('p24_description', $this->description);
        $this->setField('p24_email', $this->customer_email);
        $this->setField('p24_client', $this->client_name);
        $this->setField('p24_address', $this->address);
        $this->setField('p24_zip', $this->zip_code);
        $this->setField('p24_city', $this->city);
        $this->setField('p24_country', $this->country);
        $this->setField('p24_phone', $this->client_phone);
        $this->setField('p24_language', $this->language);
        $this->setField('p24_url_return', $this->url_return);
        $this->setField('p24_url_status', $this->url_status);
        $this->setField('p24_channel', $this->channel);
        $this->setField('p24_name_1', $this->article_name);
        $this->setField('p24_description_1', $this->article_description);
        $this->setField('p24_quantity_1', $this->article_quantity);
        $this->setField('p24_price_1', $this->article_price);
        $this->setField('p24_number_1', $this->article_number);
        $this->setField('p24_shipping', $this->shipping_cost);

        $next = 2;
        foreach ($this->additional_articles as $article) {
            $this->setField('p24_name_' . $next, $article['name']);
            $this->setField('p24_description_' . $next, $article['description']);
            $this->setField('p24_quantity_' . $next, $article['quantity']);
            $this->setField('p24_price_' . $next, $article['price']);
            $this->setField('p24_number_' . $next, $article['number']);
            ++$next;
        }

        return $this->fields;
    }

    /**
     * Register payment in payment system.
     *
     * @return RegisterResponse
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

        $this->transaction_id = uniqid();

        $response = $this->transfers24->init($this->setFields());

        return $response;
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

        return $this->transfers24->execute($token, $redirect);
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
        return $this->transfers24->receive($request->all());
    }
}
