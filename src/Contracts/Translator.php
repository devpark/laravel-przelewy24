<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Contracts;

use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Exceptions\EmptyCredentialsException;
use Devpark\Transfers24\Exceptions\NoEnvironmentChosenException;

interface Translator
{
    public function translate():Form;

    /**
     * @throws EmptyCredentialsException
     * @throws NoEnvironmentChosenException
     */
    public function configure(): Translator;

    public function getCredentials():Credentials;
}
