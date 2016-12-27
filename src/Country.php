<?php

namespace Devpark\Transfers24;

/**
 * Class Country ISO 3166.
 */
class Country extends CodeTranslate
{
    const ANDORRA = 'AD';
    const AUSTRIA = 'AT';
    const BELGIUM = 'BE';
    const CYPTUS = 'CY';
    const CZECH = 'CZ';
    const DENMARK = 'DK';
    const ESTONIA = 'EE';
    const FINLAND = 'FI';
    const FRANCE = 'FR';
    const GREESE = 'EL';
    const SPAIN = 'ES';
    const NETHERLAND = 'NL';
    const IRILAND = 'IE';
    const ISLAND = 'IS';
    const LITHUANIA = 'LT';
    const LATVIA = 'LV';
    const LUXEMBURG = 'LU';
    const MALTA = 'MT';
    const NORWAY = 'NO';
    const POLAND = 'PL';
    const PORTUGAL = 'PT';
    const SANMARINO = 'SM';
    const SLOVAKIA = 'SK';
    const SLOVENIA = 'SI';
    const SWITZERLAND = 'CH';
    const SWEDEN = 'SE';
    const HUNGARY = 'HU';
    const UK = 'GB';
    const ITALY = 'IT';
    const USA = 'US';
    const CANADA = 'CA';
    const JAPAN = 'JP';
    const UKRAINE = 'UA';
    const BELARUS = 'BY';
    const RUSSIA = 'RU';

    /**
     * Get Code language.
     *
     * @param string $country
     *
     * @return string
     */
    public static function get($country = self::POLAND)
    {
        $countryCode = self::getCode($country, static::POLAND);

        return $countryCode;
    }
}
