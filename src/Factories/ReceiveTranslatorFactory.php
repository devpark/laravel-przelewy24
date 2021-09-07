<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Factories;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Requests\Transfers24;
use Devpark\Transfers24\Translators\ReceiveTranslator;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;

class ReceiveTranslatorFactory
{
    /**
     * @var Container
     */
    private $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public function create(array $request, Credentials $credentials):ReceiveTranslator
    {
        /**
         * @var ReceiveTranslator $translator
         */
        $translator = $this->app->make(ReceiveTranslator::class);
        return $translator->init($request, $credentials)->configure();

    }
}
