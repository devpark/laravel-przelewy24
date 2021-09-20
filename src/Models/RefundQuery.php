<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class RefundQuery implements Arrayable
{
    protected $orderId;
    protected $sessionId;
    protected $amount;
    protected $description;

    public function __construct(int $orderId, string $sessionId, int $amount, string $description)
    {
        $this->orderId = $orderId;
        $this->sessionId = $sessionId;
        $this->amount = $amount;
        $this->description = $description;
    }

    public function toArray():array
    {
        return [
            'orderId' => $this->orderId,
            'sessionId' => $this->sessionId,
            'amount' => $this->amount,
            'description' => $this->description,
        ];
    }
}
