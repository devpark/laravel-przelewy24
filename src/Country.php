<?php

namespace Devpark\Transfers24;

/**
 * Class Country ISO 3166.
 */
class Country extends CodeTranslate
{
    public const ANDORRA = 'AD';

    public const AUSTRIA = 'AT';

    public const BELGIUM = 'BE';

    public const CYPTUS = 'CY';

    public const CZECH = 'CZ';

    public const DENMARK = 'DK';

    public const ESTONIA = 'EE';

    public const FINLAND = 'FI';

    public const FRANCE = 'FR';

    public const GREESE = 'EL';

    public const SPAIN = 'ES';

    public const NETHERLAND = 'NL';

    public const IRILAND = 'IE';

    public const ISLAND = 'IS';

    public const LITHUANIA = 'LT';

    public const LATVIA = 'LV';

    public const LUXEMBURG = 'LU';

    public const MALTA = 'MT';

    public const NORWAY = 'NO';

    public const POLAND = 'PL';

    public const PORTUGAL = 'PT';

    public const SANMARINO = 'SM';

    public const SLOVAKIA = 'SK';

    public const SLOVENIA = 'SI';

    public const SWITZERLAND = 'CH';

    public const SWEDEN = 'SE';

    public const HUNGARY = 'HU';

    public const UK = 'GB';

    public const ITALY = 'IT';

    public const USA = 'US';

    public const CANADA = 'CA';

    public const JAPAN = 'JP';

    public const UKRAINE = 'UA';

    public const BELARUS = 'BY';

    public const RUSSIA = 'RU';

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
