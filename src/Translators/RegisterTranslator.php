<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Translators;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Exceptions\EmptyCredentialsException;
use Devpark\Transfers24\Exceptions\NoEnvironmentChosenException;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Requests\Transfers24;
use Devpark\Transfers24\Services\Crc;
use Illuminate\Config\Repository as Config;

class RegisterTranslator extends AbstractTranslator implements Translator
{
    /**
     * @var Transfers24
     */
    private $request;

    public function init(Transfers24 $request, Credentials $credentials):RegisterTranslator{

        $this->request = $request;
        $this->credentials_keeper = $credentials;
        return $this;
    }

    public function translate():Form
   {
        $this->form = new RegisterForm();

        $session_id = uniqid();
        $this->form->addValue('sessionId', $session_id);
        $this->form->setSessionId($session_id);
        $this->form->addValue('amount', $this->request->getAmount());
        $this->form->addValue('currency', $this->request->getCurrency());
        $this->form->addValue('description', $this->request->getDescription());
        $this->form->addValue('email', $this->request->getCustomerEmail());
        $this->form->addValue('client', $this->request->getClientName());
        $this->form->addValue('address', $this->request->getAddress());
        $this->form->addValue('zip', $this->request->getZipCode());
        $this->form->addValue('city', $this->request->getCity());
        $this->form->addValue('country', $this->request->getCountry());
        $this->form->addValue('phone', $this->request->getClientPhone());
        $this->form->addValue('language', $this->request->getLanguage());

        $this->form->addValue('urlReturn', $this->request->getUrlReturn());
        $this->form->addValue('urlStatus', $this->request->getUrlStatus());
        $this->form->addValue('channel', $this->request->getChannel());

//        $this->form->addValue('p24_name_1', $this->request->getArticleName());
//        $this->form->addValue('p24_description_1', $this->request->getArticleDescription());
//        $this->form->addValue('p24_quantity_1', $this->request->getArticleQuantity());
//        $this->form->addValue('p24_price_1', $this->request->getArticlePrice());
//        $this->form->addValue('p24_number_1', $this->request->getArticleNumber());
        $this->form->addValue('shipping', $this->request->getShippingCost());

        foreach ($this->request->getCart() as $article) {
            $this->form->addValue('p24_name_', $article['name']);
            $this->form->addValue('p24_description_', $article['description']);
            $this->form->addValue('p24_quantity_', $article['quantity']);
            $this->form->addValue('p24_price_', $article['price']);
            $this->form->addValue('p24_number_', $article['number']);
        }


        $p24_api_version = $this->config->get('transfers24.version');
        $this->form->addValue('p24_api_version', $p24_api_version);
        $this->form->addValue('merchantId', $this->merchant_id);
        $this->form->addValue('posId', $this->pos_id);

        $this->form->addValue('p24_sign', $this->calculateSign());

        return $this->form;
    }

    protected function getCrcParams(): array
    {
        return ['p24_session_id', 'p24_merchant_id', 'p24_amount', 'p24_currency'];
    }
}
