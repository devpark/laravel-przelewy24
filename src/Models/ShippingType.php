<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Models;

class ShippingType
{
    public const COURIER = 0;

    public const DELIVERY_POINT = 1;

    public const PARCEL_LOCKER = 2;

    public const PACKAGE_IN_A_SHOP = 3;

    public static function all()
    {
        $types = new \ReflectionClass(ShippingType::class);

        return $types->getConstants();
    }
}
