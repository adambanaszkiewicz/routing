<?php

include '../../vendor/autoload.php';

use Requtize\Routing\RouteCollection;
use Requtize\Routing\RouteMatcher;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection as SymfonyRouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;


interface TestRoutingInterface
{
    public function getTestName();

    /**
     * STEP FIRST
     * Create list of N1 routes with N2 placeholders.
     */
    public function createCollection($routes, $placeholders);

    /**
     * STEP TWO
     * Match routes with first route in collection.
     */
    public function matchWithFirst();

    /**
     * STEP THREE
     * Match routes with last route in collection.
     */
    public function matchWithLast();

    /**
     * STEP FOUR
     * Match routes with non-existent route.
     */
    public function matchWithNone();
}

abstract class TestRoutingBase implements TestRoutingInterface
{
    protected $firstPath;
    protected $lastPath;
}

class TestRoutingSymfony extends TestRoutingBase
{
    protected $collection;
    protected $matcher;

    public function getTestName()
    {
        return 'Symfony';
    }

    public function createCollectionPre()
    {
        $this->collection = new SymfonyRouteCollection;
    }

    public function createCollection($routes, $placeholders)
    {
        for($i = 0; $i < $routes; $i++)
        {
            $phs1  = [];
            $phs2  = [];

            for($j = 0; $j < $placeholders; $j++)
            {
                $phs1[] = '{ph'.$j.'}';
                $phs2['ph'.$j] = '[a-z0-9\-]+';
            }

            $path = '/'.$i.'/pre-123/'.implode('/', $phs1).'/post-123';

            $this->collection->add(microtime(), new Route($path, ['ph2' => 'asd', 'ph3' => 'asd'], $phs2));

            if($i == 0)
                $this->firstPath = $path;

            if($i == $routes - 1)
                $this->lastPath = $path;
        }
    }

    public function matchWithFirstPre()
    {
        $context = new RequestContext('');
        $this->matcher = new UrlMatcher($this->collection, $context);

        $this->firstPath = preg_replace('/\{ph\d\}/i', 'some-segment', $this->firstPath);
        $this->lastPath  = preg_replace('/\{ph\d\}/i', 'some-segment', $this->lastPath);
    }

    public function matchWithFirst()
    {
        $this->matcher->match($this->firstPath);
    }

    public function matchWithLastPre()
    {

    }

    public function matchWithLast()
    {
        $this->matcher->match($this->lastPath);
    }

    public function matchWithNonePre()
    {

    }

    public function matchWithNone()
    {
        try
        {
            $this->matcher->match('/_____________________________unexistent-route_____________________________');
        }
        catch(Exception $e)
        {
            
        }
    }
}

class TestRoutingRequtize extends TestRoutingBase
{
    public function getTestName()
    {
        return 'Requtize';
    }

    public function createCollectionPre()
    {
        $this->collection = new RouteCollection;
    }

    public function createCollection($routes, $placeholders)
    {
        for($i = 0; $i < $routes; $i++)
        {
            $phs  = [];

            for($j = 0; $j < $placeholders; $j++)
            {
                $phs[] = '{ph'.$j.'}';
            }

            $path = '/'.$i.'/pre-123/'.implode('/', $phs).'/post-123';

            $this->collection->get(microtime(), $path, microtime());

            if($i == 0)
                $this->firstPath = $path;

            if($i == $routes - 1)
                $this->lastPath = $path;
        }

        $this->collection->compile();
    }

    public function matchWithFirstPre()
    {
        $this->matcher = new RouteMatcher($this->collection);

        $this->firstPath = preg_replace('/\{ph\d\}/i', 'some-segment', $this->firstPath);
        $this->lastPath  = preg_replace('/\{ph\d\}/i', 'some-segment', $this->lastPath);
    }

    public function matchWithFirst()
    {
        $this->matcher->matchWith($this->firstPath, 'GET');
    }

    public function matchWithLastPre()
    {

    }

    public function matchWithLast()
    {
        $this->matcher->matchWith($this->lastPath, 'GET');
    }

    public function matchWithNonePre()
    {

    }

    public function matchWithNone()
    {
        try
        {
            $this->matcher->matchWith('/_____________________________unexistent-route_____________________________', 'GET');
        }
        catch(Exception $e)
        {

        }
    }
}

class TestRoutingBootstrap
{
    protected $providers = [];

    public function add(TestRoutingInterface $provider)
    {
        $this->providers[] = $provider;

        return $this;
    }

    public function run($routes, $placeholders, $routeMatchTimes)
    {
        $result = [];
        $index  = 0;

        foreach($this->providers as $provider)
        {
            $result[$index]['test-case'] = 'Test case: '.$provider->getTestName();


            $provider->createCollectionPre();
            $start = microtime(true);
            $provider->createCollection($routes, $placeholders);
            $result[$index]['create-collection'] = number_format(microtime(true) - $start, 5);

            $provider->matchWithFirstPre();
            $start = microtime(true);

            for($i = 0; $i < $routeMatchTimes; $i++)
            {
                $provider->matchWithFirst($routes, $placeholders);
            }

            $result[$index]['match-with-first'] = number_format(microtime(true) - $start, 5);

            $provider->matchWithLastPre();
            $start = microtime(true);

            for($i = 0; $i < $routeMatchTimes; $i++)
            {
                $provider->matchWithLast($routes, $placeholders);
            }

            $result[$index]['match-with-last'] = number_format(microtime(true) - $start, 5);

            $provider->matchWithNonePre();
            $start = microtime(true);

            for($i = 0; $i < $routeMatchTimes; $i++)
            {
                $provider->matchWithNone($routes, $placeholders);
            }

            $result[$index]['match-with-none'] = number_format(microtime(true) - $start, 5);

            $index++;
        }

        return $result;
    }
}

$result = (new TestRoutingBootstrap)
    ->add(new TestRoutingRequtize)
    ->add(new TestRoutingSymfony)
    ->run(100, 4, 50);

var_dump($result);
