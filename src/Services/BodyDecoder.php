<?php

namespace Devpark\Transfers24\Services;

use Devpark\Transfers24\ErrorCode;
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

    public function decode($body):DecodedBody
    {
        $decoded_body = new DecodedBody();

        $error_message = [];
        parse_str($body, $response_table);
        foreach ($response_table as $label => $segment) {
            switch ($label) {
                case self::ERROR_LABEL:
                    $description_error = ErrorCode::getDescription($segment);
                    if (! is_null($description_error)) {
                        $error_message[$segment] = $description_error;
                    }
                    $decoded_body->setStatusCode($segment);
                    break;
                case self::TOKEN_LABEL:
                    $decoded_body->setToken($segment);
                    break;
                case self::MESSAGE_LABEL:
                    $this->segmentToDescription($segment, $error_message);
                    break;
                default:
                    $error = ErrorCode::findAccurateCode(array_keys($response_table)[0]);
                    if(! empty($error))
                    {
                        $status_code = $error;
                        $error_message[$status_code] = ErrorCode::getDescription($status_code);
                        $decoded_body->setStatusCode($status_code);
                    }
            }
        }
        $decoded_body->setErrorMessage($error_message);
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
