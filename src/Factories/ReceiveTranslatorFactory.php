<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Factories;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Requests\Transfers24;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Illuminate\Contracts\Container\Container;

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

    public function create(Transfers24 $request):RegisterTranslator
    {
        /**
         * @var RegisterTranslator $translator
         */
        $translator = $this->app->make(RegisterTranslator::class);
        return $translator->init($request);

    }
}
