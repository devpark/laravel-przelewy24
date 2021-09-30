<?php

namespace Devpark\Transfers24;

/**
 * Class Language ISO 3166.
 */
class Language extends CodeTranslate
{
    public const POLISH = 'pl';

    public const ENGLISH = 'en';

    public const SPANISH = 'es';

    public const ITALIAN = 'it';

    public const GERMAN = 'de';

    public const BULGARIA = 'bg';

    public const CZECHIA = 'cz';

    public const FRANCE = 'fr';

    public const CROATIA = 'hr';

    public const HUNGARY = 'hu';

    public const NETHERLANDS = 'nl';

    public const PORTUGAL = 'pt';

    public const SWEDEN = 'se';

    public const SLOVAKIA = 'sk';

    /**
     * Get Code language.
     *
     * @param string $language
     *
     * @return string
     */
    public static function get($language = self::POLISH)
    {
        $languageCode = static::getCode($language, static::POLISH);

        return $languageCode;
    }
}
