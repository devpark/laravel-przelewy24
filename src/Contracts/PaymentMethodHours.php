<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Contracts;

/**
 * @example:
 * {
 *       "mondayToFriday": "00-24",
 *       "saturday": "unavailable",
 *       "sunday": "00-24"
 *  }
 * @property-read string $mondayToFriday
 * @property-read string $saturday
 * @property-read string $sunday
 *
 */
interface PaymentMethodHours
{
}
