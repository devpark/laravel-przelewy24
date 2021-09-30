<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Translators;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Forms\RefundForm;
use Devpark\Transfers24\Requests\RefundRequest;
use Devpark\Transfers24\Services\Crc;
use Illuminate\Config\Repository as Config;
use Illuminate\Routing\UrlGenerator as Url;
use Illuminate\Support\Str;
use Ramsey\Uuid\UuidFactory;

class RefundTranslator extends AbstractTranslator implements Translator
{
    /**
     * @var UuidFactory
     */
    private $uuid;

    /**
     * @var Url
     */
    private $url;

    public function __construct(UuidFactory $uuid, Crc $crc, Config $config, Url $url)
    {
        parent::__construct($crc, $config);
        $this->uuid = $uuid;
        $this->url = $url;
    }

    /**
     * @var RefundRequest
     */
    private $request;

    public function init(Credentials $credentials, RefundRequest $request):RefundTranslator
    {
        $this->credentials_keeper = $credentials;
        $this->request = $request;

        return $this;
    }

    public function translate():Form
    {
        $this->form = new RefundForm();
        $this->form->addValue('requestId', $this->uuid->uuid4()->toString());
        $this->form->addValue('refundsUuid', $this->uuid->uuid4()->toString());
        $url_status = $this->getUrl($this->config->get('transfers24.url_refund_status'));
        $this->form->addValue('urlStatus', $url_status);
        $inquiries = $this->request->getRefundInquiries();
        $this->form->addValue('refunds', $inquiries);

        return $this->form;
    }

    protected function getCrcParams(): array
    {
        return [];
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
}
