<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class ShippingDetails implements Arrayable
{
    protected $type;

    protected $address;

    protected $zip;

    protected $city;

    protected $country;

    public function __construct(int $type, string $address, string $zip, string $city, string $country)
    {
        $this->type = Arr::get(ShippingType::all(), $type, ShippingType::COURIER);
        $this->address = $address;
        $this->zip = $zip;
        $this->city = $city;
        $this->country = $country;
    }

    public function toArray()
    {
        return [
            'type' => $this->type,
            'address' => $this->address,
            'zip' => $this->zip,
            'city' => $this->city,
            'country' => $this->country,
        ];
    }
}
