<?php

namespace Tests\Factories;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Currency;
use Devpark\Transfers24\Exceptions\RequestExecutionException;
use Devpark\Transfers24\Factories\HandlerFactory;
use Devpark\Transfers24\Factories\RegisterTranslatorFactory;
use Devpark\Transfers24\Factories\ResponseFactory;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Requests\Transfers24 as RequestTransfers24;
use Devpark\Transfers24\Responses\Http\Response;
use Devpark\Transfers24\Services\BodyDecoder;
use Devpark\Transfers24\Services\DecodedBody;
use Devpark\Transfers24\Services\Gateways\Transfers24;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Illuminate\Contracts\Container\Container;
use stdClass;
use Tests\UnitTestCase;
use Mockery as m;
use Illuminate\Foundation\Application;
use Devpark\Transfers24\Responses\Register as RegisterResponse;
use Devpark\Transfers24\Services\Handlers\Transfers24 as HandlersTransfers24;
use Devpark\Transfers24\Exceptions\RequestException;
use Illuminate\Http\Request;
use Illuminate\Config\Repository as Config;
use function GuzzleHttp\Psr7\build_query;

class ResponseFactoryTest extends UnitTestCase
{

    /**
     * @var ResponseFactory
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
        $this->factory = $this->app->make(ResponseFactory::class, [
            'body_decoder' => $this->body_decoder
        ]);
    }

    /** @test */
    public function create()
    {
        //Given
        $http_response = m::mock(Response::class);
        $request_parameters = ['request'];
        $http_response->shouldReceive('getFormParams')
            ->once()
            ->andReturn($request_parameters);


        $decoded_body = m::mock(DecodedBody::class);

        $body = build_query([ResponseFactory::TOKEN_LABEL => 'token']);
        $http_response->shouldReceive('getBody')
            ->once()
            ->andReturn($body);

        $this->body_decoder->shouldReceive('decode')
            ->with($body)
            ->once()
            ->andReturn($decoded_body);

        //When
        $response = $this->factory->create($http_response);

        //Then
        $this->assertSame($request_parameters, $response->getRequestParameters());
//        $this->assertSame('token', $response->getToken());
    }

}
