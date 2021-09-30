<?php

namespace Tests\Factories;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Factories\RegisterResponseFactory;
use Devpark\Transfers24\Responses\Http\Response;
use Devpark\Transfers24\Services\BodyDecoder;
use Devpark\Transfers24\Services\DecodedBody;
use Mockery as m;
use Tests\UnitTestCase;

class ResponseFactoryTest extends UnitTestCase
{
    /**
     * @var RegisterResponseFactory
     */
    private $factory;

    /**
     * @var m\MockInterface
     */
    private $body_decoder;

    protected function setUp()
    {
        parent::setUp();

        $this->body_decoder = m::mock(BodyDecoder::class);
        $this->factory = $this->app->make(RegisterResponseFactory::class, [
            'body_decoder' => $this->body_decoder,
        ]);
    }

    /** @test */
    public function create()
    {
        //Given
        $http_response = m::mock(Response::class);
        $request_parameters = m::mock(Form::class);
        $http_response->shouldReceive('getForm')
            ->once()
            ->andReturn($request_parameters);

        $decoded_body = m::mock(DecodedBody::class);

        $this->body_decoder->shouldReceive('decode')
            ->with($http_response)
            ->once()
            ->andReturn($decoded_body);

        //When
        $response = $this->factory->create($http_response);
    }
}
