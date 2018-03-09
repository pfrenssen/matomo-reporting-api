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
    protected $httpCient;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        // This sets up the mock client to respond to the first request it gets
        // with an HTTP 200 containing your mock json body.
        $mock = new MockHandler(array(new Response(200, [], 'NA')));
        $handler = HandlerStack::create($mock);
        $mockHttp = new Client(['handler' => $handler]);
        $this->httpCient = new HttpClient($mockHttp);
    }

    /**
     * Tests the getters and setters of the class.
     */
    public function testArguments()
    {
        $test_url = 'http://example.com';
        $params = array('foo' => 'bar');
        $this->httpCient->setUrl($test_url);
        $this->assertEquals($test_url, $this->httpCient->getUrl());

        $this->expectException(\InvalidArgumentException::class);
        $this->httpCient->setMethod('PUT');

        $this->httpCient->setMethod('GET');
        $this->assertEquals('GET', $this->httpCient->getMethod());
        $this->httpCient->setMethod('POST');
        $this->assertEquals('POST', $this->httpCient->getMethod());

        $this->httpCient->setRequestParams($params);
        $this->assertEquals($params, $this->httpCient->getRequestParams());
    }

    /**
     * Tests the execute method.
     */
    public function testExecute()
    {
        $this->expectException(\Exception::class);
        $this->httpCient
          ->setRequestParams(array('foo' => 'bar'))
          ->setMethod('GET')
          ->execute();

        $return = $this->httpCient
          ->setUrl('http://example.com')
          ->setRequestParams(array('foo' => 'bar'))
          ->setMethod('GET')
          ->execute();

        $this->assertTrue($return instanceof Response);
    }
}
