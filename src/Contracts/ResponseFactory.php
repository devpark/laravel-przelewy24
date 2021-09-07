<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Contracts;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\ErrorCode;
use Devpark\Transfers24\Responses\Http\Response;
use Devpark\Transfers24\Responses\Register;
use Devpark\Transfers24\Responses\Register as ResponseRegister;
use Devpark\Transfers24\Services\BodyDecoder;
use Devpark\Transfers24\Translators\RegisterTranslator;

interface ResponseFactory
{
    public function create(Response $response):IResponse;
}
