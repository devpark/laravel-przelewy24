<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Contracts;

/**
 * @example:
 * {
        "batchId": 0,
        "requestId": "string",
        "date": "string",
        "login": "string",
        "description": "string",
        "status": 3,
        "amount": 0
    }

 * @property-read  int $batchId
 * @property-read  string $requestId
 * @property-read  string $date
 * @property-read  string $login
 * @property-read  string $description
 * @property-read  int $status
 * @property-read  int $amount
 *
 */
interface RefundInfo
{
}
