<?php

namespace Requtize\Routing;

interface RouteMatcherInterface
{
     public function matchWith($path, $method = 'GET');
}
