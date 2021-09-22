<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Translators;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Exceptions\EmptyCredentialsException;
use Devpark\Transfers24\Exceptions\NoEnvironmentChosenException;
use Devpark\Transfers24\Forms\PaymentMethodsForm;
use Devpark\Transfers24\Forms\ReceiveForm;
use Devpark\Transfers24\Forms\RefundForm;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Forms\TestForm;
use Devpark\Transfers24\Requests\PaymentMethodsRequest;
use Devpark\Transfers24\Requests\RefundRequest;
use Devpark\Transfers24\Requests\Transfers24;
use Devpark\Transfers24\Services\Crc;
use Illuminate\Config\Repository as Config;

class RefundTranslator extends AbstractTranslator implements Translator
{
    /**
     * @var RefundRequest
     */
    private $request;

    public function init(Credentials $credentials, RefundRequest $request):RefundTranslator{

        $this->credentials_keeper = $credentials;
        $this->request = $request;
        return $this;
    }

    public function translate():Form
    {
        $this->form = new RefundForm();
        $this->form->addValue("requestId", "request-id");
        $this->form->addValue("refundsUuid", "refund-uuid");
        $url_status = $this->config->get('transfers24.url_refund_status');
        $this->form->addValue("urlStatus", $url_status);
        $inquiries = $this->request->getRefundInquiries();
        $this->form->addValue("refunds", $inquiries);

        return $this->form;
    }

    protected function getCrcParams(): array
    {
        return [];
    }
}
