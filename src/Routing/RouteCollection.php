<?php

namespace Requtize\Routing;

use Serializable;
use Closure;
use RuntimeException;
use InvalidArgumentException;
use Requtize\Routing\Exception\RouteNameNotFoundException;

class RouteCollection implements Serializable
{
    /**
     * Store array of routes.
     * @var array
     */
    protected $collection = [];

    /**
     * Name prefix for all added to collection Routes.
     * @var string
     */
    protected $namePrefix;

    /**
     * Path prefix for all added to collection Routes.
     * @var string
     */
    protected $pathPrefix;

    /**
     * If all routes are already compiled. After compilation,
     * no routes can be added anymore.
     * @var boolean
     */
    protected $compiled = false;

    /**
     * Construct.
     * @param array   $collection Array of routes. Can be array of arrays (compiled)
     *                            or array ob objects (prepared for compilation).
     * @param boolean $compiled   If passed collection array store compiled routes?
     * @param string  $namePrefix Routes name prefix. See: @setPrefix()
     * @param string  $pathPrefix Routes path prefix. See: @setPrefix()
     */
    public function __construct(array $collection = [], $compiled = false, $namePrefix = null, $pathPrefix = null)
    {
        $this->collection = $collection;
        $this->compiled   = $compiled;
        $this->namePrefix = $namePrefix;
        $this->pathPrefix = $pathPrefix;
    }

    /**
     * Collection is compiled already.
     * @return boolean
     */
    public function isCompiled()
    {
        return $this->compiled;
    }

    /**
     * Sets path and name prefix for all routes we add.
     * @param string $name Name prefix. For standarization, prefix should ends with dot: "prefix."
     * @param string $path Path prefix. Should starts with slash: "/prefix"
     * @return self
     */
    public function setPrefix($name, $path)
    {
        $this->namePrefix = $name;
        $this->pathPrefix = $path;

        return $this;
    }

    /**
     * Returts all Routes.
     * @return array
     */
    public function all()
    {
        return $this->collection;
    }

    /**
     * AddRoute object to collection.
     * @param Route $route
     * @return Route
     */
    public function add(Route $route)
    {
        if($this->isCompiled())
        {
            throw new RuntimeException('Cannot add new routes when Collection is compiled.');
        }

        $this->collection[] = $route;

        return $route;
    }

    /**
     * Attach route to collection, with HTTP GET method - shorthand.
     * @param  string $name   Name of route.
     * @param  string $route  Route (path).
     * @param  string $action Action to call or Closure.
     * @return Route
     */
    public function get($name, $route, $action)
    {
        return $this->create('GET', $name, $route, $action);
    }

    /**
     * Attach route to collection, with HTTP POST method - shorthand.
     * @param  string $name   Name of route.
     * @param  string $route  Route (path).
     * @param  string $action Action to call or Closure.
     * @return Route
     */
    public function post($name, $route, $action)
    {
        return $this->create('POST', $name, $route, $action);
    }

    /**
     * Attach route to collection, with HTTP PUT method - shorthand.
     * @param  string $name   Name of route.
     * @param  string $route  Route (path).
     * @param  string $action Action to call or Closure.
     * @return Route
     */
    public function put($name, $route, $action)
    {
        return $this->create('PUT', $name, $route, $action);
    }

    /**
     * Attach route to collection, with HTTP DELETE method - shorthand.
     * @param  string $name   Name of route.
     * @param  string $route  Route (path).
     * @param  string $action Action to call or Closure.
     * @return Route
     */
    public function delete($name, $route, $action)
    {
        return $this->create('DELETE', $name, $route, $action);
    }

    /**
     * Attach route to collection, with HTTP PATCH method - shorthand.
     * @param  string $name   Name of route.
     * @param  string $route  Route (path).
     * @param  string $action Action to call or Closure.
     * @return Route
     */
    public function patch($name, $route, $action)
    {
        return $this->create('PATCH', $name, $route, $action);
    }

    /**
     * Attach route to collection, with HTTP OPTIONS method - shorthand.
     * @param  string $name   Name of route.
     * @param  string $route  Route (path).
     * @param  string $action Action to call or Closure.
     * @return Route
     */
    public function options($name, $route, $action)
    {
        return $this->create('OPTIONS', $name, $route, $action);
    }

    /**
     * Attach route to collection, with HTTP HEAD method - shorthand.
     * @param  string $name   Name of route.
     * @param  string $route  Route (path).
     * @param  string $action Action to call or Closure.
     * @return Route
     */
    public function head($name, $route, $action)
    {
        return $this->create('HEAD', $name, $route, $action);
    }

    /**
     * Attach route to collection, with given arguments.
     * Used for shorthands methods above.
     * @param  string $method HTTP method to bind this route to.
     * @param  string $name   Name of route.
     * @param  string $route  Route (path).
     * @param  string $action Action to call or Closure.
     * @return Route
     */
    public function create($method, $name, $route, $action)
    {
        if($this->isCompiled())
        {
            throw new RuntimeException('Cannot add new routes when Collection is compiled.');
        }

        $route = new Route($this->namePrefix.$name, $this->pathPrefix.$route, $action);
        $route->setMethods(is_array($method) ? $method : [ $method ]);

        $this->add($route);

        return $route;
    }

    /**
     * Merges current collection with given collection.
     * @param  RouteCollection $collection Collection object from we merge routes.
     * @return self
     */
    public function mergeWith(RouteCollection $collection)
    {
        $this->collection = array_merge($this->collection, $collection->all());

        return $this;
    }

    /**
     * Allows to create group of Routes, with defined name and path prefix.
     * @param  string  $name     Name prefix applied for all routes.
     * @param  string  $path     Path prefix applied for all routes.
     * @param  Closure $callback Closure callback, which is called to create new collection.
     *                           Passed argument is new RouteCOllection object You should
     *                           use to add new Routes.
     * @return self
     */
    public function group($name, $path, Closure $callback)
    {
        if($this->isCompiled())
        {
            throw new RuntimeException('Cannot add new routes when Collection is compiled.');
        }

        $collection = new self;
        $collection->setPrefix($name, $path);

        $callback($collection);

        $this->mergeWith($collection);

        return $this;
    }

    /**
     * Compile all added routes with default compiler.
     * @return self;
     */
    public function compile()
    {
        return $this->compileWithCompiler(new RouteDefinitionCompiler);
    }

    /**
     * Compile all added routes with given compiler object.
     * @param  RouteDefinitionCompilerInterface|null $compiler Compiler object.
     * @return self
     */
    public function compileWithCompiler(RouteDefinitionCompilerInterface $compiler = null)
    {
        if($this->isCompiled())
        {
            throw new RuntimeException('Cannot recompile when Collection is compiled.');
        }

        if(! $compiler)
        {
            $compiler = new RouteDefinitionCompiler;
        }

        foreach($this->collection as $key => $route)
        {
            $compiler->compile($route);

            $this->collection[$key] = $this->transformRouteToArray($route);
        }

        $this->compiled = true;

        return $this;
    }

    /**
     * Finds Route by given name.
     * @param  string $name Route name to find.
     * @throws RouteNameNotFoundException When Route with given name not found.
     * @return self
     */
    public function findByName($name)
    {
        if($this->compiled)
        {
            foreach($this->collection as $route)
            {
                if($route['name'] == $name)
                {
                    return $this->transformArrayToRoute($route);
                }
            }
        }
        else
        {
            foreach($this->collection as $route)
            {
                if($route->getName() == $name)
                {
                    return $route;
                }
            }
        }

        throw new RouteNameNotFoundException("Route named $name not found.");
    }

    /**
     * Exports all Routes to array, except routes that contains Action as Callback function.
     * @return array
     */
    public function exportToArray()
    {
        $result = [];

        if($this->compiled)
        {
            foreach($this->collection as $route)
            {
                // Export only when Route has string action (not Closure or anonymous function)
                if(is_string($route['action']))
                {
                    $result[] = $route;
                }
            }
        }
        else
        {
            foreach($this->collection as $route)
            {
                // Export only when Route has string action (not Closure or anonymous function)
                if(is_string($route->getAction()))
                {
                    $result[] = $route;
                }
            }
        }

        return $result;
    }

    /**
     * Imports Routes from given array.
     * @param  array  $array Array with Routes details.
     * @return self
     */
    public function importFromArray(array $array)
    {
        foreach($array as $route)
        {
            $this->collection[] = $this->transformArrayToRoute($this->fixRouteArray($route));
        }

        return $this;
    }

    /**
     * Transforms Route object to PHP array.
     * @param  Route  $route Route object to transform.
     * @return array
     */
    public function transformRouteToArray(Route $route)
    {
        return  [
            'name'    => $route->getName(),
            'action'  => $route->getAction(),
            'rules'   => $route->getRules(),
            'methods' => $route->getMethods(),
            'route'   => $route->getRoute(),
            'sourceRoute' => $route->getSourceRoute(),
            'defaults' => $route->getDefaults(),
            'extras'   => $route->getExtras(),
            'tokens'   => $route->getTokens(),
            'arguments'=> $route->getArguments()
        ];
    }

    /**
     * Transforms Route array to Route object.
     * @param  array  $route Route array with details.
     * @return Route
     */
    public function transformArrayToRoute(array $route)
    {
        if(! isset($route['name']) || !isset($route['sourceRoute']) || ! isset($route['action']))
        {
            throw new InvalidArgumentException('Each Route must contains name, sourceRoute and action indexes.');
        }

        $obj = new Route($route['name'], $route['sourceRoute'], $route['action']);

        if(isset($route['rules']))
            $obj->setRules($route['rules']);
        if(isset($route['methods']))
            $obj->setMethods($route['methods']);
        if(isset($route['route']))
            $obj->setRoute($route['route']);
        if(isset($route['defaults']))
            $obj->setDefaults($route['defaults']);
        if(isset($route['extras']))
            $obj->setExtras($route['extras']);
        if(isset($route['tokens']))
            $obj->setTokens($route['tokens']);
        if(isset($route['arguments']))
            $obj->setArguments($route['arguments']);

        return $obj;
    }

    /**
     * Take array as argument, and fix undefined indexes in this array,
     * and return fixed array.
     * @param  array  $route Array with some undefined indexes.
     * @return array  Fixed array.
     */
    public function fixRouteArray(array $route)
    {
        if(! isset($route['name']))
            $route['name'] = '';
        if(! isset($route['sourceRoute']))
            $route['sourceRoute'] = '';
        if(! isset($route['action']))
            $route['action'] = '';
        if(! isset($route['rules']))
            $route['rules'] = [];
        if(! isset($route['methods']))
            $route['methods'] = [];
        if(! isset($route['route']))
            $route['route'] = '';
        if(! isset($route['defaults']))
            $route['defaults'] = [];
        if(! isset($route['extras']))
            $route['extras'] = [];
        if(! isset($route['tokens']))
            $route['tokens'] = [];

        return $route;
    }

    public function serialize()
    {
        return serialize([$this->collection, $this->compiled]);
    }

    public function unserialize($data)
    {
        list($this->collection, $this->compiled) = unserialize($data);
    }
}
