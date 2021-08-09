<?php

namespace Devpark\Transfers24\Factories;

use Devpark\Transfers24\Responses\Http\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Response.
 */
class HttpResponseFactory
{
    public function create(array $form_params, ResponseInterface $response):Response
    {
        $http_response = new Response();
        $http_response->addFormParams($form_params);
        $http_response->addStatusCode($response->getStatusCode());
        $http_response->addBody($response->getBody());

        return $http_response;
    }
}
