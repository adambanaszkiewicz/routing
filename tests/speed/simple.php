<?php

include '../../vendor/autoload.php';

use Requtize\Routing\RouteCollection;
use Requtize\Routing\RouteMatcher;

class SpeedTest
{
    protected $possiblePaths = [
        [ 'path' => '/', 'arguments' => [], 'wildcard' => true ],
        [ 'path' => '/simple', 'arguments' => [], 'wildcard' => true ],
        [ 'path' => '/multiple/segments/asdr/asdf/awer/34t/vw45t/ew4f/5tw4/56/wb4/t5w3/56/5/87m67k/9/75/u5/td/3s/2/werq3w/456/e5/8', 'arguments' => [], 'wildcard' => true ],
        [ 'path' => '/123356/2345/467/45678/345/213/456/436587/5879/865/23/', 'arguments' => [], 'wildcard' => true ],
        [ 'path' => '/SoMeBiGlEtTeRs/In/PaTh', 'arguments' => [], 'wildcard' => true ],
        [ 'path' => '/sections-with-dashes/asd-1443/12-12-1233', 'arguments' => [], 'wildcard' => true ],
        [ 'path' => '///multiple/slashes////nextto', 'arguments' => [], 'wildcard' => true ],
        [ 'path' => '/slash-at-the-end/', 'arguments' => [], 'wildcard' => true ],
        [ 'path' => '/sudyfguew/rtbwerfgvs/dftg45y/sb', 'arguments' => [], 'wildcard' => true ],
        [ 'path' => '/a/s/d/f/g/h/j/k/l', 'arguments' => [], 'wildcard' => true ],
        [ 'path' => '/~`/!@#$%^&*/()_+-={}|[]/\:";\'<>?,/./', 'arguments' => [], 'wildcard' => true ],
        [ 'path' => '/some/{argument}', 'arguments' => ['argument'], 'wildcard' => true ],
        [ 'path' => '/{arg1}/{arg2}-{arg3}', 'arguments' => ['arg1', 'arg2', 'arg3'], 'wildcard' => true ],
        [ 'path' => '/{arg1?}', 'arguments' => ['arg1'], 'wildcard' => true ],
        [ 'path' => '/{q}/{w}/{e}', 'arguments' => ['q', 'w', 'e'], 'wildcard' => true ],
        [ 'path' => '/23452456-{arg}', 'arguments' => ['arg'], 'wildcard' => true ],
        [ 'path' => '/{arg1}/{arg2?}', 'arguments' => ['arg1', 'arg2'], 'wildcard' => true ],
        [ 'path' => '/{arg1?}', 'arguments' => ['arg1'], 'wildcard' => true ],
        [ 'path' => '/{arg1:digit}', 'arguments' => ['arg1'], 'wildcard' => true ],
        [ 'path' => '/{arg1:number}/{arg2:alnum}/{arg3:anlumd}', 'arguments' => ['arg1', 'arg2', 'arg3'], 'wildcard' => true ],
        [ 'path' => '/{arg1}/{arg2:number?}', 'arguments' => ['arg1', 'arg2'], 'wildcard' => true ]
    ];

    protected $possibleMethods = ['get', 'post', 'put', 'delete', 'patch', 'options', 'head'];

    public function start($generateRoutes, $passess)
    {
        $tests = [];

        for($i = 0; $i < $passess; $i++)
        {
            $tests[$i] = [
                'time-create-collection' => 0,
                'time-compilation' => 0,
                'time-match-unexistent-route' => 0,
                'time-total-with-compilation' => 0,
                'time-import-from-array' => 0,
                'time-match-unexistent-route-imported' => 0,
                'time-total-without-compilation' => 0
            ];

            $collection = new RouteCollection;

            $start = microtime(true);

            for($j = 0; $j < $generateRoutes; $j++)
            {
                $method = rand(1, 20) == 1 ? $this->possibleMethods[rand(0, 6)] : 'get';
                $path   = $this->possiblePaths[rand(0, count($this->possiblePaths) - 1)];

                $collection->{$method}(microtime(), $path['path'], microtime());
            }

            $tests[$i]['time-create-collection'] = number_format(microtime(true) - $start, 4);

            // ===================================================================================

            $start = microtime(true);

            $collection->compile();

            $tests[$i]['time-compilation'] = number_format(microtime(true) - $start, 4);

            // ===================================================================================

            $matcher = new RouteMatcher($collection);

            $start = microtime(true);

            try
            {
                $matcher->matchWith('/_____________________________unexistent-route_____________________________', 'GET');
            }
            catch(Exception $e)
            {

            }

            $tests[$i]['time-match-unexistent-route'] = number_format(microtime(true) - $start, 4);
            $tests[$i]['time-total-with-compilation'] = $tests[$i]['time-create-collection'] + $tests[$i]['time-compilation'] + $tests[$i]['time-match-unexistent-route'];


            // ===================================================================================


            $exported = $collection->exportToArray();

            $start = microtime(true);

            $newCollection = new RouteCollection($exported, $collection->isCompiled());

            $tests[$i]['time-import-from-array'] = number_format(microtime(true) - $start, 4);


            // ===================================================================================


            $matcher = new RouteMatcher($collection);

            $start = microtime(true);

            try
            {
                $matcher->matchWith('/_____________________________unexistent-route_____________________________', 'GET');
            }
            catch(Exception $e)
            {

            }

            $tests[$i]['time-match-unexistent-route-imported'] = number_format(microtime(true) - $start, 4);
            $tests[$i]['time-total-without-compilation'] = $tests[$i]['time-import-from-array'] + $tests[$i]['time-match-unexistent-route-imported'];
        }

        return $tests;
    }
}


$test = new SpeedTest;
var_dump($test->start(1000, 5));
