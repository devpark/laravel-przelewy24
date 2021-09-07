<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Factories;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\ResponseFactory;
use Devpark\Transfers24\ErrorCode;
use Devpark\Transfers24\Responses\Http\Response;
use Devpark\Transfers24\Responses\Register;
use Devpark\Transfers24\Responses\Register as ResponseRegister;
use Devpark\Transfers24\Services\BodyDecoder;
use Devpark\Transfers24\Translators\RegisterTranslator;

class RegisterResponseFactory implements ResponseFactory
{
    /**
     * @var BodyDecoder
     */
    private $body_decoder;

    public function __construct(BodyDecoder $body_decoder)
    {
        $this->body_decoder = $body_decoder;
    }

    public function create(Response $response):IResponse
    {
        $decoded_body = $this->body_decoder->decode($response->getBody());

        return new Register($response->getForm(), $decoded_body);
    }
}
