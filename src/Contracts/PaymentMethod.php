<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Contracts;

/**
 * @example:
 * {
        "name": "string",
        "id": 0,
        "status": true,
        "imgUrl": "string",
        "mobileImgUrl": "string",
        "mobile": true,
        "availabilityHours":
            {
            "mondayToFriday": "00-24",
            "saturday": "unavailable",
            "sunday": "00-24"
            }

    }
 * @property-read string $name
 * @property-read int $id
 * @property-read bool $status
 * @property-read string $imgUrl
 * @property-read string $mobileImgUrl
 * @property-read bool $mobile
 * @property-read PaymentMethodHours $availabilityHours
 *
 */
interface PaymentMethod
{
}
