<?php

namespace Matomo\ReportingApi\tests;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Matomo\ReportingApi\HttpClient;
use Matomo\ReportingApi\RequestFactoryInterface;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Tests for the HttpClient class.
 *
 * @coversDefaultClass \Matomo\ReportingApi\HttpClient
 */
class HttpClientTest extends TestCase
{

    /**
     * @param array $parameters
     *
     * @covers ::getRequestParameters
     * @dataProvider requestParametersProvider
     */
    public function testGetRequestParameters(array $parameters)
    {
        $http_client = $this->getHttpClient();

        // By default the parameters are empty.
        $this->assertEmpty($http_client->getRequestParameters());

        // Check that any parameters that are set are correctly returned.
        $http_client->setRequestParameters($parameters);
        $this->assertEquals($parameters, $http_client->getRequestParameters());
    }

    /**
     * @param array $parameters
     *
     * @covers ::setRequestParameters
     * @dataProvider requestParametersProvider
     */
    public function testSetRequestParameters(array $parameters)
    {
        $http_client = $this->getHttpClient();

        // Check that the object itself is returned for chaining.
        $result = $http_client->setRequestParameters($parameters);
        $this->assertEquals($http_client, $result);

        // Check that the parameters have been correctly set.
        $this->assertEquals($parameters, $http_client->getRequestParameters());
    }

    /**
     * Data provider returning request parameters.
     */
    public function requestParametersProvider()
    {
        return [
            [[]],
            [['foo']],
            [['foo' => 'bar']],
            [['foo', 'bar']],
            [['foo' => 'bar', 'baz' => 'qux']],
        ];
    }

    /**
     * @param string $url
     *   A valid URL.
     *
     * @covers ::getUrl
     * @dataProvider urlProvider
     */
    public function testGetUrl($url)
    {
        $http_client = $this->getHttpClient();

        // By default the URL is empty.
        $this->assertEmpty($http_client->getUrl());

        // Check that any URL that is set is correctly returned.
        $http_client->setUrl($url);
        $this->assertEquals($url, $http_client->getUrl());
    }

    /**
     * @param string $url
     *   A valid URL.
     *
     * @covers ::setUrl
     * @dataProvider urlProvider
     */
    public function testSetUrl($url)
    {
        $http_client = $this->getHttpClient();

        // Check that the object itself is returned for chaining.
        $result = $http_client->setUrl($url);
        $this->assertEquals($http_client, $result);

        // Check that the URL has been correctly set.
        $this->assertEquals($url, $http_client->getUrl());
    }

    /**
     * @param string $invalid_url
     *   An invalid URL.
     *
     * @expectedException \InvalidArgumentException
     * @dataProvider invalidUrlProvider
     */
    public function testInvalidUrl($invalid_url)
    {
        $this->getHttpClient()->setUrl($invalid_url);
    }

    /**
     * Data provider returning valid URLs.
     */
    public function urlProvider()
    {
        return [
            ['http://example.com'],
            ['http://exa-mple.com/pa-th/'],
            ['https://archive.example.net/'],
            ['https://archive.example.net./'],
            ['http://127.0.0.1'],
            ['ftp://a.b.com'],
            ['http://localhost/'],
            ['irc://user:password@localhost:8000/path'],
            ['irc://:@localhost:'],
        ];
    }

    /**
     * Data provider returning invalid URLs.
     */
    public function invalidUrlProvider()
    {
        return [
            ['http://exa_mple.com'],
            ['archive.example.net'],
            ['irc://:@:/path'],
            ['http://..com'],
            ['/'],
            [null],
            [true],
            [false],
            [''],
            [[]],
            [-1],
            [0],
            [1],
            [2e-2],
        ];
    }

    /**
     * @param string $method
     *   A supported HTTP method.
     *
     * @covers ::getMethod
     * @dataProvider supportedHttpMethodsProvider
     */
    public function testGetMethod($method)
    {
        $http_client = $this->getHttpClient();

        // The GET method should be set by default.
        $this->assertEquals('GET', $http_client->getMethod());

        // Check that the method that was set is returned correctly.
        $http_client->setMethod($method);
        $this->assertEquals($method, $http_client->getMethod());
    }

    /**
     * @param string $method
     *   A supported HTTP method.
     *
     * @covers ::setMethod
     * @dataProvider supportedHttpMethodsProvider
     */
    public function testSetMethod($method)
    {
        // It is expected that the given HTTP method will be passed to the request factory.
        $request_factory = $this->prophesize(RequestFactoryInterface::class);
        $request_factory
            ->getRequest($method, Argument::any())
            ->willReturn($this->prophesize(RequestInterface::class)->reveal())
            ->shouldBeCalled();

        $http_client = $this->getHttpClient(null, $request_factory->reveal());
        $http_client->setUrl('http://example.com');
        $result = $http_client->setMethod($method);
        // Send the request so that we can check if the correct HTTP method is used.
        $http_client->sendRequest();

        // Check that the client is returned for chaining.
        $this->assertEquals($http_client, $result);
    }

    /**
     * Tests that an exception is thrown for unsupported HTTP methods.
     *
     * @param string $invalid_method
     *   An unsupported or invalid HTTP method.
     *
     * @covers ::setMethod
     * @expectedException \InvalidArgumentException
     * @dataProvider unsupportedHttpMethodsProvider
     */
    public function testUnsupportedHttpMethods($invalid_method)
    {
        $http_client = $this->getHttpClient();
        $http_client->setMethod($invalid_method);
    }

    /**
     * Data provider returning supported HTTP methods.
     */
    public function supportedHttpMethodsProvider()
    {
        return [
            ['GET'],
            ['POST'],
        ];
    }

    /**
     * Data provider returning unsupported and invalid HTTP methods.
     */
    public function unsupportedHttpMethodsProvider()
    {
        return [
            // Unsupported HTTP methods.
            ['CONNECT'],
            ['DELETE'],
            ['HEAD'],
            ['OPTIONS'],
            ['PUT'],
            ['TRACE'],
            // Lowercase HTTP methods are invalid.
            ['connect'],
            ['delete'],
            ['get'],
            ['head'],
            ['options'],
            ['post'],
            ['put'],
            ['trace'],
            // Some arguments that are no HTTP methods.
            [null],
            [true],
            [false],
            [''],
            [0],
            [1],
            [-1],
            ['0'],
        ];
    }

    /**
     * Tests that an exception is thrown when sending a request without specifying a URL.
     *
     * @covers ::sendRequest
     * @expectedException \Exception
     */
    public function testSendRequestWithoutUrl()
    {
        $this->getHttpClient()
            ->setRequestParameters(['foo' => 'bar'])
            ->setMethod('GET')
            ->sendRequest();
    }

    /**
     * @covers ::sendRequest
     * @dataProvider sendRequestProvider
     */
    public function testSendRequest($method, $url, $parameters, $expected_options)
    {
        // Mock the response that will be returned by the Guzzle HTTP client.
        $response = $this->prophesize(Response::class);

        // Mock the request that is expected to be returned by the request factory and will be passed to Guzzle.
        $request = $this->prophesize(RequestInterface::class);

        // It is expected that the request factory will be called with the correct method and URL.
        $request_factory = $this->prophesize(RequestFactoryInterface::class);
        $request_factory->getRequest($method, $url)
            ->willReturn($request->reveal())
            ->shouldBeCalled();

        // It is expected that the send() method will be called on the Guzzle client with the request and the expected
        // options array. This will return the response.
        $client = $this->prophesize(ClientInterface::class);
        $client->send($request, $expected_options)
            ->willReturn($response->reveal())
            ->shouldBeCalled();

        $http_client = $this->getHttpClient($client->reveal(), $request_factory->reveal());
        $http_client->setMethod($method);
        $http_client->setUrl($url);
        $http_client->setRequestParameters($parameters);

        $response = $http_client->sendRequest();
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * Provides data for testing the sendRequest() method.
     */
    public function sendRequestProvider()
    {
        return [
            [
                // The HTTP method to use.
                'GET',
                // The URL to use.
                'http://example.com',
                // The request parameters to use.
                [
                    'format' => 'json',
                    'module' => 'API',
                    'method' => 'API.getMatomoVersion',
                ],
                // The expected options that should be passed to the Guzzle HTTP client.
                [
                    'query' => [
                        'format' => 'json',
                        'module' => 'API',
                        'method' => 'API.getMatomoVersion',
                    ],
                ]
            ],
            [
                // The HTTP method to use.
                'POST',
                // The URL to use.
                'https://example.com',
                // The request parameters to use.
                [
                    'format' => 'json',
                    'module' => 'API',
                    'method' => 'API.getMatomoVersion',
                ],
                // The expected options that should be passed to the Guzzle HTTP client.
                [
                    'form_params' => [
                        'format' => 'json',
                        'module' => 'API',
                        'method' => 'API.getMatomoVersion',
                    ],
                ]
            ],
        ];
    }

    /**
     * Returns the SUT.
     *
     * @return \Matomo\ReportingApi\HttpClient
     *   The class being tested.
     */
    protected function getHttpClient(ClientInterface $httpClient = null, RequestFactoryInterface $requestFactory = null)
    {
        $httpClient = $httpClient ?: $this->prophesize(ClientInterface::class)->reveal();
        $requestFactory = $requestFactory ?: $this->prophesize(RequestFactoryInterface::class)->reveal();
        return new HttpClient($httpClient, $requestFactory);
    }
}
