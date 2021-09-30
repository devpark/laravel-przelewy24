<?php

namespace Tests\Services;

use Devpark\Transfers24\Responses\Http\Response;
use Devpark\Transfers24\Services\BodyDecoder;
use Tests\UnitTestCase;

class BodyDecoderTest extends UnitTestCase
{
    /**
     * @var BodyDecoder
     */
    private $body_decoder;

    protected function setUp()
    {
        parent::setUp();

        $this->body_decoder = new BodyDecoder();
    }

    /** @test */
    public function decode_token()
    {
        $body = json_encode([
            BodyDecoder::DATA_LABEL => [
                BodyDecoder::TOKEN_LABEL => 'token',
            ],
            BodyDecoder::RESPONSE_CODE => 0,
        ]);
        $status_code = 200;

        $response = $this->whenReceivedApiResponse($status_code, $body);

        $decoded = $this->body_decoder->decode($response);

        $this->assertSame($status_code, $decoded->getStatusCode());
        $this->assertSame('token', $decoded->getToken());
    }

    /** @test */
    public function decode_test_connection()
    {
        $body = json_encode([
            BodyDecoder::DATA_LABEL => true,
            BodyDecoder::ERROR_LABEL => 'string',
        ]);
        $status_code = 200;

        $response = $this->whenReceivedApiResponse($status_code, $body);

        $decoded = $this->body_decoder->decode($response);

        $this->assertSame($status_code, $decoded->getStatusCode());
    }

    /** @test */
    public function decode_error()
    {
        $body = json_encode([
            BodyDecoder::ERROR_LABEL => 'getting error',
            BodyDecoder::ERROR_CODE => 400,
        ]);
        $status_code = 400;

        $response = $this->whenReceivedApiResponse($status_code, $body);

        $decoded = $this->body_decoder->decode($response);

        $this->assertSame(400, $decoded->getStatusCode());
    }

    /** @test */
    public function decode_error_message()
    {
        $body = json_encode([
            BodyDecoder::ERROR_LABEL => 'getting error',
            BodyDecoder::ERROR_CODE => 400,
        ]);

        $status_code = 400;

        $response = $this->whenReceivedApiResponse($status_code, $body);

        $decoded = $this->body_decoder->decode($response);

        $this->assertSame('getting error', $decoded->getErrorMessage());
    }

    /**
     * @param int $status_code
     * @param $body
     * @return \Mockery\MockInterface
     */
    private function whenReceivedApiResponse(int $status_code, $body): \Mockery\MockInterface
    {
        $response = \Mockery::mock(Response::class);
        $response->shouldReceive('getStatusCode')
            ->once()
            ->andReturn($status_code);
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn($body);

        return $response;
    }
}
