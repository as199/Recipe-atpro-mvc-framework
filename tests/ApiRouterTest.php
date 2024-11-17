<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Atpro\mvc\Config\api\ApiRouter;

class ApiRouterTest extends TestCase
{
    private ApiRouter $router;

    protected function setUp(): void
    {
        parent::setUp();
        $this->router = new ApiRouter('/api/test');
    }

    public function testRouteMatching(): void
    {
        $this->router->get('/api/test', 'TestController@index');
        
        $reflection = new \ReflectionClass($this->router);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        
        $routes = $routesProperty->getValue($this->router);
        $this->assertArrayHasKey('GET', $routes);
        $this->assertCount(1, $routes['GET']);
    }

    public function testDifferentHttpMethods(): void
    {
        $this->router->get('/api/test', 'TestController@index');
        $this->router->post('/api/test', 'TestController@store');
        $this->router->put('/api/test', 'TestController@update');
        $this->router->delete('/api/test', 'TestController@delete');
        
        $reflection = new \ReflectionClass($this->router);
        $routesProperty = $reflection->getProperty('routes');
        $routesProperty->setAccessible(true);
        
        $routes = $routesProperty->getValue($this->router);
        
        $this->assertArrayHasKey('GET', $routes);
        $this->assertArrayHasKey('POST', $routes);
        $this->assertArrayHasKey('PUT', $routes);
        $this->assertArrayHasKey('DELETE', $routes);
    }
} 