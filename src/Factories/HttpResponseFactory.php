<?php

namespace Devpark\Transfers24\Factories;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Responses\Http\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Response.
 */
class HttpResponseFactory
{
    public function create(Form $form, ResponseInterface $response):Response
    {
        $http_response = new Response();
        $http_response->addFormParams($form);
        $http_response->addStatusCode($response->getStatusCode());
        $http_response->addBody($response->getBody()->getContents());
        return $http_response;
    }
}
