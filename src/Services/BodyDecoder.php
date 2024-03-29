<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Services;

use Devpark\Transfers24\Responses\Http\Response;
use Illuminate\Support\Arr;

/**
 * Class Amount.
 */
class BodyDecoder
{
    public const ERROR_LABEL = 'error';

    /**
     * Token key in transfers24 response.
     */
    public const TOKEN_LABEL = 'token';

    /**
     * Error description key in transfers24 response.
     */
    public const MESSAGE_LABEL = 'errorMessage';

    public const DATA_LABEL = 'data';

    public const RESPONSE_CODE = 'responseCode';

    public const ERROR_CODE = 'code';

    public function decode(Response $response):DecodedBody
    {
        $decoded_body = new DecodedBody();

        $decoded_body->setStatusCode($response->getStatusCode());

        $response_table = json_decode($response->getBody(), true);

        if (Arr::has($response_table, 'data')) {
            $data = Arr::get($response_table, 'data');
            $decoded_body->setData($data);
        }

        if (Arr::has($response_table, 'data.token')) {
            $token = Arr::get($response_table, 'data.token');
            $decoded_body->setToken($token);
        }

        if (Arr::has($response_table, 'error')) {
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
        if (count($transform_error_segment) > 1) {
            $error_message[$transform_error_segment[0]] = $transform_error_segment[1];
        }
        $error_message[] = $segment;
    }
}
