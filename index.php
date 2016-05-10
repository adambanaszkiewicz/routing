<?php

use Requtize\Routing\RouteCollection;
use Requtize\Routing\Route;
use Requtize\Routing\RouteDefinitionCompiler;
use Requtize\Routing\RouteCreator;
use Requtize\Routing\RouteMatcher;

error_reporting(-1);

include 'vendor/autoload.php';

$collection = new RouteCollection;

// Homepage => callback
/*$collection->add(new Route('/', function($request, $response) {
    echo $response;
}));

// Homepage => controller definition
$collection->add(new Route('/', 'Controller:action'));

// Named route
$collection->add(new Route('/', 'Controller:action'))
    ->setName('homepage');

// Argument in path
$collection->add(new Route('/{argument}', 'Controller:action'));

// Multiple arguments in path
$collection->add(new Route('/{argument1}/{argument2}/{argument3}', 'Controller:action'));

// Optional argument in path
$collection->add(new Route('/{argument?}', 'Controller:action'));

// Optional argument in path II
$collection->add(new Route('/{argument}/{argument2?}', 'Controller:action'));

// Defined arguments
$collection->add(new Route('/{argument}/{argument2}', 'Controller:action'))
    ->setRules(['argument1' => '\d', 'argument2' => '[a-z]+']);

// Defined argument and optional defined argument
$collection->add(new Route('/{argumentWith-MANY_chars12365dD}/{argument2?}', 'Controller:action'))
    ->setRules(['argument1' => '\d', 'argument2' => '[a-z]+']);*/

// Full options route
$collection->add(new Route('full-option', '/full/{argument1}/{argument2}', 'Controller:action'))
    ->setRules(['argument2' => 'word', 'argument1' => 'digit'])
    ->setMethods(['POST'])
    ->addMethod('GET');

$collection->get('homepage', '/', 'Homepage:Homepage');

$collection->group('blog.', '/blog', function($collection) {
    $collection->get('homepage', '', function($request, $response) {

    });

    $collection->get('show', '/{id}', 'Blog:show');
    $collection->get('delete', '/delete/{id}', 'Blog:delete');
});

$collection->get('asd', '/path-to/{digit}/asd/{alnum}/{number}', 'Homepage:Homepage');

$compiler = new RouteDefinitionCompiler;
//$compiler->addPredefinedRule('alias', '[a-zA-Z0-9\-]+');

$collection->compileWithCompiler($compiler);


$creator = new RouteCreator($collection);
var_dump($creator->create('full-option', ['argument2' => 'alias', 'argument1' => 1]));


/*$exported = $collection->export();
$collection2 = new RouteCollection;
$collection2->importFromArray($exported);
var_dump($collection, $collection2);*/


//var_dump($collection->findByName('full-option'));
//var_dump($collection->findByName('asd'));

$matcher = new RouteMatcher($collection);

var_dump($matcher->matchWith(str_replace(str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']), '', $_SERVER['REQUEST_URI']), 'GET'/* $_SERVER['REQUEST_METHOD'] */));
