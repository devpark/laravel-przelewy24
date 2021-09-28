<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Responses;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\Refund;
use Devpark\Transfers24\Exceptions\TestConnectionException;

class NotificationResponse
{
    /**
     * @var array
     */
    private $notification;

    public function __construct(array $receive_data)
    {
        $this->notification = $receive_data;
    }

    /**
     * @return Refund[]
     */
    public function getNotification():array
    {
        return $this->notification;
    }

    public function getResponse():string
    {
        return 'ok';
    }
}
