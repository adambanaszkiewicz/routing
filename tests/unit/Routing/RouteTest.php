<?php

use Requtize\Routing\Route;

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function testContructor()
    {
        $route = new Route('name', 'sourceRoute', 'action');

        $this->assertEquals('name', $route->getName());
        $this->assertEquals('sourceRoute', $route->getSourceRoute());
        $this->assertEquals('action', $route->getAction());
    }

    public function testMethods()
    {
        $route = new Route('name', 'sourceRoute', 'action');

        $this->assertEquals($route->getMethods(), ['GET']);

        $route->setMethods(['POST']);

        $this->assertEquals($route->getMethods(), ['POST']);

        $route->addMethod('HEAD');

        $this->assertEquals($route->getMethods(), ['POST', 'HEAD']);

        $route->removeMethod('POST');

        $this->assertEquals($route->getMethods(), ['HEAD']);

        $this->assertEquals($route->isSatisfiedByMethod('HEAD'), true);
        $this->assertEquals($route->isSatisfiedByMethod('GET'), false);
        
        $route->addMethod('GET');

        $this->assertEquals($route->isSatisfiedByMethod('GET'), true);
    }

    public function testRules()
    {
        $route = new Route('name', 'sourceRoute', 'action');

        $this->assertEquals($route->getRules(), []);

        $route->setRules(['rule1' => '1', 'rule2' => 2]);

        $this->assertEquals($route->getRules(), ['rule1' => '1', 'rule2' => 2]);

        $route->addRule('name', 'value');

        $this->assertEquals($route->getRules(), ['rule1' => '1', 'rule2' => 2, 'name' => 'value']);

        $route->removeRule('rule2');

        $this->assertEquals($route->getRules(), ['rule1' => '1', 'name' => 'value']);

        $this->assertEquals($route->getRuleByName('rule1'), '1');
        $this->assertEquals($route->getRuleByName('name'), 'value');
        $this->assertEquals($route->getRuleByName('unexistent-rule'), null);
    }
}
