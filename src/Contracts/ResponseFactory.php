<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Contracts;

use Devpark\Transfers24\Responses\Http\Response;

interface ResponseFactory
{
    public function create(Response $response):IResponse;
}
