<?php

namespace Devpark\Transfers24\Services;

use Devpark\Transfers24\ErrorCode;
use Devpark\Transfers24\Responses\Http\Response;
use Illuminate\Support\Arr;
use Psr\Http\Message\StreamInterface;

/**
 * Class Amount.
 */
class BodyDecoder
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

    const DATA_LABEL = 'data';

    const RESPONSE_CODE = 'responseCode';

    const ERROR_CODE = 'code';

    public function decode(Response $response):DecodedBody
    {
        $decoded_body = new DecodedBody();

        $decoded_body->setStatusCode($response->getStatusCode());

        $response_table = json_decode($response->getBody(), true);

        if (Arr::has($response_table, 'data.token')){
            $token = Arr::get($response_table, 'data.token');
            $decoded_body->setToken($token);
        }

        if (Arr::has($response_table, 'error')){
            $error_message = Arr::get($response_table, 'error');
            $decoded_body->setErrorMessage($error_message);
        }

        return $decoded_body;
    }


    /**
     * Get error pair key and value from string and store in error_message.
     *
     * @param string $segment
     *
     * @return void
     */
    protected function segmentToDescription($segment, array &$error_message)
    {
        $transform_error_segment = explode(':', $segment);
        if(count($transform_error_segment) > 1)
        {
            $error_message[$transform_error_segment[0]] = $transform_error_segment[1];
        }
        $error_message[] = $segment;
    }
}
