<?php

namespace Tests\Services;

use Devpark\Transfers24\ErrorCode;
use Devpark\Transfers24\Services\BodyDecoder;
use Tests\UnitTestCase;
use function GuzzleHttp\Psr7\build_query;

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
        $body = build_query([BodyDecoder::TOKEN_LABEL => 'token']);

        $decoded = $this->body_decoder->decode($body);

        $this->assertSame('token', $decoded->getToken());
    }

    /** @test */
    public function decode_error()
    {
        $body = build_query([BodyDecoder::ERROR_LABEL => 'error_code']);

        $decoded = $this->body_decoder->decode($body);

        $this->assertSame( 'error_code', $decoded->getStatusCode());
    }

    /** @test */
    public function decode_error_message()
    {
        $body = build_query([BodyDecoder::ERROR_LABEL => ErrorCode::ERR00]);

        $decoded = $this->body_decoder->decode($body);

        $this->assertSame( ErrorCode::ERR00, $decoded->getStatusCode());
        $this->assertSame( [ErrorCode::ERR00 => ErrorCode::getDescription(ErrorCode::ERR00)], $decoded->getErrorMessage());
    }

    /** @test */
    public function decode_message_label()
    {
        $code = 'code';
        $message = 'message';
        $body = build_query([BodyDecoder::MESSAGE_LABEL => $code .':'.$message]);

        $decoded = $this->body_decoder->decode($body);

        $this->assertSame( [
            $code => $message,
            0 => $code .':'.$message
        ], $decoded->getErrorMessage());
    }


    /** @test */
    public function decode_accurate_error()
    {
        $body = build_query([ErrorCode::ERR00 => ErrorCode::ERR00]);

        $decoded = $this->body_decoder->decode($body);

        $this->assertSame( ErrorCode::ERR00, $decoded->getStatusCode());
        $this->assertSame( [ErrorCode::ERR00 => ErrorCode::getDescription(ErrorCode::ERR00)], $decoded->getErrorMessage());
    }

}
