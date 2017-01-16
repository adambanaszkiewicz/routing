<?php

use Requtize\Routing\RouteCollection;
use Requtize\Routing\RouteCreator;
use Requtize\Routing\Route;

class RouteCreatorTest extends PHPUnit_Framework_TestCase
{
    protected $collection;
    protected $creator;

    protected function setUp()
    {
        $this->collection = new RouteCollection;

        $this->collection->get('no-tokens', '/no-tokens', '');
        $this->collection->get('one-token', '/one-token/{token}', '');
        $this->collection->get('eight-tokens', '/eight-tokens/{token1}/{token2}/{token3}/{token4}/{token5}/{token6}/{token7}/{token8}', '');
        $this->collection->get('token-digit', '/token-digit/{token}', '')->addRule('token', 'digit');

        $this->collection->compile();

        $this->creator = new RouteCreator($this->collection);
    }

    protected function tearDown()
    {
        $this->collection = null;
        $this->creator = null;
    }

    /**
     * @expectedException Requtize\Routing\Exception\RouteNameNotFoundException
     */
    public function testCreateNotExistent()
    {
        $this->creator->create('___non-existent-name___');
    }

    public function testCreateWithoutTokens()
    {
        $this->assertEquals('/no-tokens', $this->creator->create('no-tokens'));
    }

    public function testCreateOneToken()
    {
        $this->assertEquals('/one-token/token-value', $this->creator->create('one-token', ['token' => 'token-value']));
    }

    public function testCreateEightTokens()
    {
        $this->assertEquals('/eight-tokens/1/2/3/4/5/6/7/8', $this->creator->create('eight-tokens', [
            'token1' => '1',
            'token2' => '2',
            'token3' => '3',
            'token4' => '4',
            'token5' => '5',
            'token6' => '6',
            'token7' => '7',
            'token8' => '8'
        ]));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Route 'one-token' require 'token' argument.
     */
    public function testCreateWithTokenNotSet()
    {
        $this->assertEquals('/one-token/token-value', $this->creator->create('one-token'));
    }

    public function testCreateWithTokenPredefinedTypeSuccess()
    {
        $this->assertEquals('/token-digit/3', $this->creator->create('token-digit', ['token' => 3]));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Required parameter 'token' for route 'token-digit' has wrong type.
     */
    public function testCreateWithTokenPredefinedTypeError()
    {
        $this->creator->create('token-digit', ['token' => 33]);
    }

    public function testDecorate()
    {
        $collection = new RouteCollection;
        $collection->get('no-tokens', '/no-tokens', '');
        $collection->get('token-digit', '/token-digit/{token}', '')->addRule('token', 'digit');
        $collection->compile();

        $creator = new MyRouteCreator($collection);

        $this->assertEquals('/before/no-tokens/after', $creator->create('no-tokens'));
        $this->assertEquals('/before/token-digit/1/after', $creator->create('token-digit', ['token' => '1']));
    }

    public function testMoreArgumentsThanNeed()
    {
        $this->assertEquals('/one-token/1?more=values&in=query+string+%3A%29', $this->creator->create('one-token', [
            'token' => '1',
            'more'  => 'values',
            'in'    => 'query string :)'
        ]));
    }
}

class MyRouteCreator extends RouteCreator
{
    public function decorate($path)
    {
        return '/before'.$path.'/after';
    }
}
