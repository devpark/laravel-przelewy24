<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Models;

class ShippingType
{
    const COURIER = 0;
    const DELIVERY_POINT = 1;
    const PARCEL_LOCKER = 2;
    const PACKAGE_IN_A_SHOP = 3;

    public static function all(){
        $types = new \ReflectionClass(ShippingType::class);

        return $types->getConstants();
    }
}
