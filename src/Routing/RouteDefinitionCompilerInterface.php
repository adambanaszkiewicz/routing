<?php

namespace Requtize\Routing;

interface RouteDefinitionCompilerInterface
{
    public function compile(Route $route);
}
