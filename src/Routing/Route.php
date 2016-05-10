<?php

namespace Requtize\Routing;

class Route
{
    protected $name;
    protected $action;
    protected $rules = [];
    protected $methods = ['GET'];
    protected $route;
    protected $sourceRoute;

    /**
     * Store tokens founded in route.
     * @var array
     */
    protected $tokens = [];

    /**
     * Store arguments takes from URL.
     * @var array
     */
    protected $arguments = [];

    /**
     * Store default values for URL tokens.
     * @var array
     */
    protected $defaults = [];

    /**
     * Extra parameters, stored with this route.
     * @var array
     */
    protected $extras = [];

    public function __construct($name, $sourceRoute, $action)
    {
        $this->setSourceRoute($sourceRoute);
        $this->setAction($action);
        $this->setName($name);
    }

    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setMethods(array $methods)
    {
        $this->methods = $methods;

        return $this;
    }

    public function getMethods()
    {
        return array_values($this->methods);
    }

    public function addMethod($method)
    {
        $this->methods[] = $method;

        return $this;
    }

    public function removeMethod($method)
    {
        if(($key = array_search($method, $this->methods)) !== false)
        {
            unset($this->methods[$key]);
        }

        return $this;
    }

    public function isSatisfiedByMethod($method)
    {
        return in_array($method, $this->methods);
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setRules(array $rules)
    {
        $this->rules = $rules;

        return $this;
    }

    public function addRule($argument, $rule)
    {
        $this->rules[$argument] = $rule;

        return $this;
    }

    public function removeRule($rule)
    {
        if(isset($this->rules[$rule]))
        {
            unset($this->rules[$rule]);
        }

        return $this;
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function getRuleByName($name)
    {
        return isset($this->rules[$name]) ? $this->rules[$name] : null;
    }

    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setSourceRoute($sourceRoute)
    {
        $this->sourceRoute = $sourceRoute;

        return $this;
    }

    public function getSourceRoute()
    {
        return $this->sourceRoute;
    }

    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    public function getDefaults()
    {
        return $this->defaults;
    }

    public function setExtras(array $extras)
    {
        $this->extras = $extras;

        return $this;
    }

    public function getExtras()
    {
        return $this->extras;
    }

    public function setTokens(array $tokens)
    {
        $this->tokens = $tokens;

        return $this;
    }

    public function getTokens()
    {
        return $this->tokens;
    }
}
