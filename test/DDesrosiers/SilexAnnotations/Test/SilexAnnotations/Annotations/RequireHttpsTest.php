<?php

namespace DDesrosiers\SilexAnnotations\Test\Annotations;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use DDesrosiers\SilexAnnotations\AnnotationServiceProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;

class RequireHttpsTest extends \PHPUnit_Framework_TestCase
{
    /** @var Application */
    protected $app;

    /** @var Client */
    protected $client;

    public function setUp()
    {
        $this->app = new Application();
        $this->app['debug'] = true;

        $this->app->register(new AnnotationServiceProvider(), array(
            "annot.srcDir" => __DIR__."/../../../../../../src",
            "annot.controllers" => array("DDesrosiers\\SilexAnnotations\\Test\\Annotations\\RequireHttpsTestController")
        ));
    }

    public function testHttps()
    {
        $client = new Client($this->app, array('REQUEST_SCHEME' => 'https'));
        $client->request("GET", "/test");
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testHttp()
    {
        // we make the request as http, but it should be redirected to a https request
        $request = Request::create('http://example.com/test');
        $response = $this->app->handle($request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('https://example.com/test'));
    }
}

class RequireHttpsTestController
{
    /**
     * @SLX\Request(method="GET", uri="/test")
     * @SLX\RequireHttps
     */
    public function testRequireHttps()
    {
        return new Response();
    }
}