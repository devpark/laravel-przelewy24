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


        $this->form->addValue('shipping', $this->request->getShippingCost());
        $this->form->addValue('method', $this->request->getMethod());
        $this->form->addValue('timeLimit', $this->request->getTimeLimit());
        $this->form->addValue('waitForResult', $this->request->getWaitForResult());
        $this->form->addValue('regulationAccept', $this->request->getRegulationAccept());
        $this->form->addValue('transferLabel', $this->request->getTransferLabel());
        $this->form->addValue('mobileLib', $this->request->getMobileLib());
        $this->form->addValue('sdkVersion', $this->request->getSdkVersion());
        $this->form->addValue('encoding', $this->request->getEncoding());
        $this->form->addValue('methodRefId', $this->request->getMethodRefId());

//            $this->form->addValue('sellerId', $article['sellerId']);
//            $this->form->addValue('sellerCategory', $article['sellerCategory']);
//            $this->form->addValue('name', $article['name']);
//            $this->form->addValue('description', $article['description']);
//            $this->form->addValue('quantity', $article['quantity']);
//            $this->form->addValue('price', $article['price']);
//            $this->form->addValue('number', $article['number']);

        $this->form->addValue('additional', ['shipping'  => $this->request->getShippingDetails()]);
        $this->form->addValue('cart', $this->request->getCart());


        $p24_api_version = $this->config->get('transfers24.version');
        $this->form->addValue('p24_api_version', $p24_api_version);
        $this->form->addValue('merchantId', $this->merchant_id);
        $this->form->addValue('posId', $this->pos_id);

        $this->form->addValue('sign', $this->calculateSign());

        return $this->form;
    }

    protected function getCrcParams(): array
    {
        return ['p24_session_id', 'p24_merchant_id', 'p24_amount', 'p24_currency'];
    }
}
