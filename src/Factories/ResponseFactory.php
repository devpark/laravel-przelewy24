<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Factories;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\ErrorCode;
use Devpark\Transfers24\Responses\Http\Response;
use Devpark\Transfers24\Responses\Register;
use Devpark\Transfers24\Responses\Register as ResponseRegister;
use Devpark\Transfers24\Services\BodyDecoder;
use Devpark\Transfers24\Translators\RegisterTranslator;

class ResponseFactory
{
    const ERROR_LABEL = 'error';
    /**
     * Token key in transfers24 response.
     */
    const TOKEN_LABEL = 'token';

    /**
     * Error description key in transfers24 response.
     */
    const MESSAGE_LABEL = 'errorMessage';
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
        $request_parameters = $response->getFormParams();

        $decoded_body = $this->body_decoder->decode($response->getBody());

        return new Register($request_parameters, $decoded_body);
    }
}
