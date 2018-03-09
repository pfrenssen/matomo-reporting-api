<?php

namespace Piwik\ReportingApi\tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Piwik\ReportingApi\HttpClient;

class HttpClientTest extends TestCase
{

    /**
     * The query factory object.
     *
     * @var \Piwik\ReportingApi\HttpClient
     */
    protected $httpClient;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        // This sets up the mock client to respond to the first request it gets
        // with an HTTP 200 containing your mock json body.
        $mock = new MockHandler([new Response(200, [], 'NA')]);
        $handler = HandlerStack::create($mock);
        $mockHttp = new Client(['handler' => $handler]);
        $this->httpClient = new HttpClient($mockHttp);
    }

    /**
     * Tests the getters and setters of the class.
     */
    public function testArguments()
    {
        $test_url = 'http://example.com';
        $params = ['foo' => 'bar'];
        $this->httpClient->setUrl($test_url);
        $this->assertEquals($test_url, $this->httpClient->getUrl());

        $this->expectException(\InvalidArgumentException::class);
        $this->httpClient->setMethod('PUT');

        $this->httpClient->setMethod('GET');
        $this->assertEquals('GET', $this->httpClient->getMethod());
        $this->httpClient->setMethod('POST');
        $this->assertEquals('POST', $this->httpClient->getMethod());

        $this->httpClient->setRequestParams($params);
        $this->assertEquals($params, $this->httpClient->getRequestParams());
    }

    /**
     * Tests the execute method.
     */
    public function testExecute()
    {
        $this->expectException(\Exception::class);
        $this->httpClient
          ->setRequestParams(['foo' => 'bar'])
          ->setMethod('GET')
          ->execute();

        $return = $this->httpClient
          ->setUrl('http://example.com')
          ->setRequestParams(['foo' => 'bar'])
          ->setMethod('GET')
          ->execute();

        $this->assertTrue($return instanceof Response);
    }
}
