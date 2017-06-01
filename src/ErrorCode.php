<?php

namespace Devpark\Transfers24;

/**
 * Class ErrorCode.
 */
class ErrorCode extends CodeTranslate
{
    const ERR00 = 'err00';
    const ERR01 = 'err01';
    const ERR02 = 'err02';
    const ERR03 = 'err03';
    const ERR04 = 'err04';
    const ERR05 = 'err05';
    const ERR06 = 'err06';
    const ERR07 = 'err07';
    const ERR08 = 'err08';
    const ERR09 = 'err09';
    const ERR10 = 'err10';
    const ERR49 = 'err49';
    const ERR51 = 'err51';
    const ERR52 = 'err52';
    const ERR53 = 'err53';
    const ERR54 = 'err54';
    const ERR55 = 'err55';
    const ERR56 = 'err56';
    const ERR57 = 'err57';
    const ERR58 = 'err58';
    const ERR101 = 'err101';
    const ERR102 = 'err102';
    const ERR103 = 'err103';
    const ERR104 = 'err104';
    const ERR105 = 'err105';
    const ERR106 = 'err106';
    const ERR161 = 'err161';
    const ERR162 = 'err162';

    /**
     * List of error description.
     *
     * @var array
     */
    protected static $error_description = [
        self::ERR00  => 'Incorrect call',
        self::ERR01  => 'Authorization answer confirmation was not received.',
        self::ERR02  => 'Authorization answer was not received.',
        self::ERR03  => 'This query has been already processed.',
        self::ERR04  => 'Authorization query incomplete or incorrect.',
        self::ERR05  => 'Store configuration cannot be read.',
        self::ERR06  => 'Saving of authorization query failed.',
        self::ERR07  => 'Another payment is being concluded.',
        self::ERR08  => 'Undetermined store connection status.',
        self::ERR09  => 'Permitted corrections amount has been exceeded.',
        self::ERR10  => 'Incorrect transaction value!',
        self::ERR49  => 'To high transaction risk factor.',
        self::ERR51  => 'Incorrect reference method.',
        self::ERR52  => 'Incorrect feedback on session information!',
        self::ERR53  => 'Transaction error !',
        self::ERR54  => 'Incorrect transaction value!',
        self::ERR55  => 'Incorrect transaction id!',
        self::ERR56  => 'Incorrect card',
        self::ERR57  => 'Incompatibility of TEST flag !',
        self::ERR58  => 'Incorrect sequence number !',
        self::ERR101 => 'Incorrect call.',
        self::ERR102 => 'Allowed transaction time has expired .',
        self::ERR103 => 'Incorrect transfer value.',
        self::ERR104 => 'Transaction awaits confirmation.',
        self::ERR105 => 'Transaction finished after allowed time.',
        self::ERR106 => 'Transaction result verification',
        self::ERR161 => 'Transaction request terminated by user.',
        self::ERR162 => 'Transaction request terminated by user.',
    ];

    /**
     * Get description of error from transfers24.
     *
     * @param string $name
     *
     * @return string|null
     */
    public static function getDescription($name)
    {
        $codes = static::getCodes();

        if (in_array($name, $codes)) {
            return self::$error_description[$name];
        }

        return;
    }

    /**
     * Find error code by name
     *
     * @param $name
     *
     * @return mixed
     */
    public static function findAccurateCode($name)
    {
        return collect(static::getCodes())->filter(function($code) use ($name){
            return strstr($name, $code);
        })->first();
    }
}
