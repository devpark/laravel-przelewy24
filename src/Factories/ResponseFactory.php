<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Factories;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\ErrorCode;
use Devpark\Transfers24\Responses\Http\Response;
use Devpark\Transfers24\Responses\Register as ResponseRegister;
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

    public function create(Response $response):IResponse
    {
        $request_parameters = $response->getFormParams();

        return new ResponseRegister($request_parameters);
    }


    /**
     * Set properties from HttpResponse.
     *
     * @return void
     */
    public function convertResponse(Response $response)
    {

        $response_table = [];
        parse_str($response->getBody(), $response_table);

        foreach ($response_table as $label => $segment) {
            switch ($label) {
                case self::ERROR_LABEL:
                    $description_error = ErrorCode::getDescription($segment);
                    if (! is_null($description_error)) {
                        $this->error_message[$segment] = $description_error;
                    }
                    $this->status_code = $segment;
                    break;
                case self::TOKEN_LABEL:
                    $this->token = $segment;
                    break;
                case self::MESSAGE_LABEL:
                    $this->segmentToDescription($segment);
                    break;
                default:
                    $error = ErrorCode::findAccurateCode(array_keys($response_table)[0]);
                    if(! empty($error))
                    {
                        $this->status_code = $error;
                        $this->error_message[$this->status_code] = ErrorCode::getDescription($this->status_code);
                    }
            }
        }
    }

    /**
     * Get error pair key and value from string and store in error_message.
     *
     * @param string $segment
     *
     * @return void
     */
    protected function segmentToDescription($segment)
    {
        $transform_error_segment = explode(':', $segment);
        if(count($transform_error_segment) > 1)
        {
            $this->error_message[$transform_error_segment[0]] = $transform_error_segment[1];
        }
        $this->error_message[] = $segment;
    }
}
