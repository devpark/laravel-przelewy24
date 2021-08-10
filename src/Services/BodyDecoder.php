<?php

namespace Devpark\Transfers24\Services;

/**
 * Class Amount.
 */
class BodyDecoder
{
    public function decode():DecodedBody
    {
        return new DecodedBody();

        //assign decoded_body to body_decoder
//        parse_str($response->getBody(), $response_table);
//        foreach ($response_table as $label => $segment) {
//            switch ($label) {
//                case self::ERROR_LABEL:
//                    $description_error = ErrorCode::getDescription($segment);
//                    if (! is_null($description_error)) {
//                        $error_message[$segment] = $description_error;
//                    }
//                    $converted->setStatusCode($segment);
//                    break;
//                case self::TOKEN_LABEL:
//                    $converted->setToken($segment);
//                    break;
//                case self::MESSAGE_LABEL:
//                    $this->segmentToDescription($segment, $error_message);
//                    break;
//                default:
//                    $error = ErrorCode::findAccurateCode(array_keys($response_table)[0]);
//                    if(! empty($error))
//                    {
//                        $status_code = $error;
//                        $error_message[$status_code] = ErrorCode::getDescription($status_code);
//                        $converted->setStatusCode($status_code);
//                    }
//            }
//        }

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
