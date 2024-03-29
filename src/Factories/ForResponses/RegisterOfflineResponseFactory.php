<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Factories\ForResponses;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\ResponseFactory;
use Devpark\Transfers24\Responses\Http\Response;
use Devpark\Transfers24\Responses\RegisterOfflineResponse;
use Devpark\Transfers24\Services\BodyDecoder;

class RegisterOfflineResponseFactory implements ResponseFactory
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
        $decoded_body = $this->body_decoder->decode($response);

        return new RegisterOfflineResponse($response->getForm(), $decoded_body);
    }
}
