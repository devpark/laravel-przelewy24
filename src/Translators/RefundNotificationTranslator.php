<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Translators;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Forms\RefundNotificationForm;
use Devpark\Transfers24\Services\Crc;
use Illuminate\Config\Repository as Config;

class RefundNotificationTranslator extends AbstractTranslator implements Translator
{
    public function __construct(Crc $crc, Config $config)
    {
        parent::__construct($crc, $config);
    }

    /**
     * @var array
     */
    private $notification_data;

    public function init(Credentials $credentials, array $notification_data):RefundNotificationTranslator
    {
        $this->credentials_keeper = $credentials;
        $this->notification_data = $notification_data;

        return $this;
    }

    public function translate():Form
    {
        $this->form = new RefundNotificationForm();
        $order_id = $this->notification_data['orderId'];
        $this->form->addValue('orderId', $order_id);

        return $this->form;
    }

    protected function getCrcParams(): array
    {
        return [];
    }
}
