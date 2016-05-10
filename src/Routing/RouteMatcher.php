<?php

namespace Requtize\Routing;

use Requtize\Routing\Exception\RouteNotFoundException;

class RouteMatcher implements RouteMatcherInterface
{
    protected $collection;

    public function __construct(RouteCollection $collection)
    {
        $this->collection = $collection;
    }

    public function matchWith($path, $method = 'GET')
    {
        if($this->collection->isCompiled() === false)
        {
            $this->collection->compile();
        }

        foreach($this->collection->all(true) as $route)
        {
            if($route['tokens'] === [])
            {
                if(preg_match($route['route'], $path) && in_array($method, $route['methods']))
                {
                    return $this->collection->transformArrayToRoute($route);
                }
            }
            else
            {
                if(preg_match($route['route'], $path, $matches) && in_array($method, $route['methods']))
                {
                    unset($matches[0]);

                    $route['arguments'] = array_values($matches);
                    
                    return $this->collection->transformArrayToRoute($route);
                }
            }
        }

        throw new RouteNotFoundException("Route <b>\"$path\"</b> not found.");
    }
}
