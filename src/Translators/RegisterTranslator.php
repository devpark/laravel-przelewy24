<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Translators;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Requests\Transfers24;

class RegisterTranslator
{

    /**
     * @var Transfers24
     */
    private $request;

    public function init(Transfers24 $request):RegisterTranslator{
        $this->request = $request;
        return $this;
    }

    public function translate():RegisterForm
    {

    }
}
