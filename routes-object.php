<?php

/**
 * Simple Homepage with callback function.
 */
$collection->add(new Route('o-homepage-callback', '/', function($request, $response) {
    return $response;
}));

/**
 * Homepage with controller definition.
 */
$collection->add(new Route('o-homepage-controller-definition', '/', 'Controller:action'));

/**
 * Page with one argument.
 */
$collection->add(new Route('o-with-one-argument', '/{argument}', 'Controller:action'));

/**
 * Page with segment and one argument.
 */
$collection->add(new Route('o-with-segment-one-argument', '/segment/{argument}', 'Controller:action'));

/**
 * Page with optional argument.
 */
$collection->add(new Route('o-optional-argument', '/{argument?}', 'Controller:action'));

/**
 * Page with segment and optional argument.
 */
$collection->add(new Route('o-segment-optional-argument', '/segment/{argument?}', 'Controller:action'));

/**
 * Page with three arguments.
 */
$collection->add(new Route('o-three-arguments', '/{argument1}/{argument2}/{argument3}', 'Controller:action'));

/**
 * Page with three arguments, last optional.
 */
$collection->add(new Route('o-three-arguments-last-optional', '/{argument1}/{argument2}/{argument3?}', 'Controller:action'));

/**
 * Page with three optional arguments.
 */
$collection->add(new Route('o-three-arguments-last-optional', '/{argument1?}/{argument2?}/{argument3?}', 'Controller:action'));

/**
 * Page with one predefined argument.
 */
$collection->add(new Route('o-predefined-argument', '/{argument1:number}', 'Controller:action'));

/**
 * Page with 4 predefined arguments.
 */
$collection->add(new Route('o-predefined-argument', '/{argument1:number}-{argument2:number}-{argument3:number}/{argument4:alnumd}', 'Controller:action'));
