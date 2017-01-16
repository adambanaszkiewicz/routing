<?php

namespace Requtize\Routing;

use InvalidArgumentException;
use Requtize\Routing\Exception\RouteNameNotFoundException;

class RouteCreator
{
    protected $collection;

    public function __construct(RouteCollection $collection)
    {
        $this->collection = $collection;
    }

    public function decorate($path, $arguments)
    {
        if($arguments !== [])
            $query = http_build_query($arguments);
        else
            $query = '';

        return $path.($query ? '?'.$query : '');
    }

    public function create($name, array $arguments = [])
    {
        try
        {
            $route = $this->collection->findByName($name);
        }
        catch(RouteNameNotFoundException $e)
        {
            throw $e;
        }

        $arguments = array_merge($route->getDefaults(), $arguments);

        if($route->getTokens() === [])
        {
            $source = $route->getSourceRoute();
        }
        else
        {
            $source = $route->getSourceRoute();

            foreach($route->getTokens() as $token)
            {
                if(! isset($arguments[$token['name']]))
                {
                    throw new InvalidArgumentException("Route '{$name}' require '{$token['name']}' argument.");
                }
                else
                {
                    $argument = $arguments[$token['name']];

                    if(! preg_match('/^'.$token['pattern'].'$/', $argument))
                    {
                        throw new InvalidArgumentException("Required parameter '{$token['name']}' for route '{$name}' has wrong type.");
                    }

                    unset($arguments[$token['name']]);

                    $source = str_replace($token['placeholder'], $argument, $source);
                }
            }
        }

        return $this->decorate($source, $arguments);
    }
}
