<?php

use Requtize\Routing\RouteCollection;
use Requtize\Routing\Route;

class RouteCollectionTest extends PHPUnit_Framework_TestCase
{
    protected $collection;

    protected function setUp()
    {
        $this->collection = new RouteCollection;
    }

    protected function tearDown()
    {
        $this->collection = null;
    }

    /**
     * Add route as object, and check if is in collection.
     */
    public function testAddRouteAsObject()
    {
        $this->collection->add(new Route('name', '/', 'action'));
        $this->collection->compile();

        $this->assertCount(1, $this->collection->all());
    }

    /**
     * Add route as shorthand method, with GET method
     * and check if exists and if has GET method.
     */
    public function testAddRouteShorthandMethodGet()
    {
        $this->collection->get('name', '/', 'action');

        $this->assertCount(1, $this->collection->all());

        $routes = $this->collection->all();

        $this->assertArrayHasKey('0', $routes);

        $this->assertContains('GET', $routes[0]->getMethods());
    }

    /**
     * Add route as shorthand method, with POST method
     * and check if exists and if has POST method.
     */
    public function testAddRouteShorthandMethodPost()
    {
        $this->collection->post('name', '/', 'action');

        $this->assertCount(1, $this->collection->all());

        $routes = $this->collection->all();

        $this->assertArrayHasKey('0', $routes);

        $this->assertContains('POST', $routes[0]->getMethods());
    }

    /**
     * Add route as shorthand method, with PUT method
     * and check if exists and if has PUT method.
     */
    public function testAddRouteShorthandMethodPut()
    {
        $this->collection->put('name', '/', 'action');

        $this->assertCount(1, $this->collection->all());

        $routes = $this->collection->all();

        $this->assertArrayHasKey('0', $routes);

        $this->assertContains('PUT', $routes[0]->getMethods());
    }

    /**
     * Add route as shorthand method, with DELETE method
     * and check if exists and if has GET DELETE.
     */
    public function testAddRouteShorthandMethodDelete()
    {
        $this->collection->delete('name', '/', 'action');

        $this->assertCount(1, $this->collection->all());

        $routes = $this->collection->all();

        $this->assertArrayHasKey('0', $routes);

        $this->assertContains('DELETE', $routes[0]->getMethods());
    }

    /**
     * Add route as shorthand method, with PATCH method
     * and check if exists and if has PATCH method.
     */
    public function testAddRouteShorthandMethodPatch()
    {
        $this->collection->patch('name', '/', 'action');

        $this->assertCount(1, $this->collection->all());

        $routes = $this->collection->all();

        $this->assertArrayHasKey('0', $routes);

        $this->assertContains('PATCH', $routes[0]->getMethods());
    }

    /**
     * Add route as shorthand method, with OPTIONS method
     * and check if exists and if has GET OPTIONS.
     */
    public function testAddRouteShorthandMethodOptions()
    {
        $this->collection->options('name', '/', 'action');

        $this->assertCount(1, $this->collection->all());

        $routes = $this->collection->all();

        $this->assertArrayHasKey('0', $routes);

        $this->assertContains('OPTIONS', $routes[0]->getMethods());
    }

    /**
     * Add route as shorthand method, with HEAD method
     * and check if exists and if has HEAD method.
     */
    public function testAddRouteShorthandMethodHead()
    {
        $this->collection->head('name', '/', 'action');

        $this->assertCount(1, $this->collection->all());

        $routes = $this->collection->all();

        $this->assertArrayHasKey('0', $routes);

        $this->assertContains('HEAD', $routes[0]->getMethods());
    }

    /**
     * Add route as create() method call, with POST method
     * and check if exists and if has POST method.
     */
    public function testAddRouteCreateMethodPostMethod()
    {
        $this->collection->create('POST', 'name', '/', 'action');

        $this->assertCount(1, $this->collection->all());

        $routes = $this->collection->all();

        $this->assertArrayHasKey('0', $routes);

        $this->assertContains('POST', $routes[0]->getMethods());
    }

    /**
     * Create two collections, merge it and checks if base collection
     * has two routes.
     */
    public function testMerge()
    {
        $this->collection->get('current', '/current', 'actionCurrent');

        $newCollection = new RouteCollection;
        $newCollection->get('new', '/', 'action');

        $this->collection->mergeWith($newCollection);

        $routes = $this->collection->all();

        $this->assertCount(2, $routes);

        $this->assertArrayHasKey('0', $routes);
        $this->assertArrayHasKey('1', $routes);

        $this->assertEquals('current', $routes[0]->getName());
        $this->assertEquals('/current', $routes[0]->getSourceRoute());

        $this->assertEquals('new', $routes[1]->getName());
        $this->assertEquals('/', $routes[1]->getSourceRoute());
    }

    /**
     * Sets name and path prefix, add route to collection and
     * check if route name and path has prefixes at the beginning of each.
     */
    public function testAddRouteAndCheckPrefix()
    {
        $this->collection->setPrefix('name-prefix.', '/path-prefix');
        $this->collection->get('name', '/path', 'action');

        $this->assertCount(1, $this->collection->all());

        $routes = $this->collection->all();

        $this->assertArrayHasKey('0', $routes);

        $this->assertEquals('name-prefix.name', $routes[0]->getName());
        $this->assertEquals('/path-prefix/path', $routes[0]->getSourceRoute());
    }

    /**
     * Create group with "blog" prefix, and add one route in group,
     * and one route outside group.
     */
    public function testCreateGroup()
    {
        $this->collection->get('name', '/path', 'action');

        $this->collection->group('blog.', '/blog', function($collection) {
            $collection->get('name', '/path', 'action');
        });


        $routes = $this->collection->all();

        $this->assertCount(2, $routes);

        $this->assertArrayHasKey('0', $routes);
        $this->assertArrayHasKey('1', $routes);

        $this->assertEquals('name', $routes[0]->getName());
        $this->assertEquals('/path', $routes[0]->getSourceRoute());

        $this->assertEquals('blog.name', $routes[1]->getName());
        $this->assertEquals('/blog/path', $routes[1]->getSourceRoute());
    }

    /**
     * Add three routes, and find for the second by Name.
     */
    public function testFindByName()
    {
        // Protect to search only first index
        $this->collection->get('first', '/first', 'action');
        $this->collection->get('second', '/second', 'action');
        // protect to search only in last index.
        $this->collection->get('third', '/third', 'action');


        $route = $this->collection->findByName('second');

        $this->assertInstanceOf('Requtize\Routing\Route', $route);

        $this->assertEquals('/second', $route->getSourceRoute());
    }

    /**
     * Create three routes, one with callback action, two with string actions.
     * Should export only these two with string actions.
     */
    public function testExportToArray()
    {
        $this->collection->get('first', '/first', 'action');
        $this->collection->get('second', '/second', function() {});
        $this->collection->get('third', '/third', 'action');

        $this->collection->compile();

        $exported = $this->collection->exportToArray();

        $this->assertCount(2, $exported);

        $this->assertArrayHasKey('0', $exported);
        $this->assertArrayHasKey('1', $exported);

        $this->assertEquals('first', $exported[0]['name']);
        $this->assertEquals('/first', $exported[0]['sourceRoute']);

        $this->assertEquals('third', $exported[1]['name']);
        $this->assertEquals('/third', $exported[1]['sourceRoute']);
    }

    /**
     * Create array with full functionality, and try to import it - then check if
     * all functionality is in imported route.
     *
     * @dataProvider importArray
     */
    public function testImport($data)
    {
        $import = [];

        foreach($data as $field)
        {
            $import[$field['name']] = $field['value'];
        }

        $this->collection->importFromArray([$import]);

        $routes = $this->collection->all();

        $this->assertCount(1, $routes);

        foreach($data as $field)
        {
            $this->assertEquals($field['value'], $routes[0][$field['name']]);
        }
    }

    public function importArray()
    {
        return [
            [
                [
                    [
                        'name'   => 'name',
                        'value'  => 'simple'
                    ],
                    [
                        'name'   => 'action',
                        'value'  => 'Controller:action'
                    ],
                    [
                        'name'   => 'rules',
                        'value'  => ['first-token' => '[a-z]+', 'second-token' => '\/[^\/]+']
                    ],
                    [
                        'name'   => 'methods',
                        'value'  => ['GET', 'POST']
                    ],
                    [
                        'name'   => 'route',
                        'value'  => '/source/route/([a-z]+)(\/[^\/]+)'
                    ],
                    [
                        'name'   => 'sourceRoute',
                        'value'  => '/source/route/{first-token}/{second-token}'
                    ],
                    [
                        'name'   => 'defaults',
                        'value'  => ['first-token' => 'default', 'second-token' => '/wild/card']
                    ],
                    [
                        'name'   => 'wildcard',
                        'value'  => 'second-token'
                    ],
                    [
                        'name'   => 'extras',
                        'value'  => ['first-extra', 'second-extra']
                    ],
                    [
                        'name'   => 'tokens',
                        'value'  => [
                            [
                                'name' => 'first-token',
                                'optional' => false,
                                'wildcard' => false,
                                'pattern' => '[a-z]+'
                            ],
                            [
                                'name' => 'second-token',
                                'optional' => false,
                                'wildcard' => true,
                                'pattern' => '\/[^\/]+'
                            ]
                        ]
                    ],
                    [
                        'name'   => 'compiled',
                        'value'  => false
                    ],
                ]
            ]
        ];
    }

    /**
     * Add two simple routes, compile them and check if all exists.
     */
    public function testCompile()
    {
        $this->collection->get('first', '/first', 'action');
        $this->collection->get('second', '/second', function() {});

        $this->collection->compile();

        $routes = $this->collection->all();

        $this->assertCount(2, $routes);

        $this->assertArrayHasKey('0', $routes);
        $this->assertArrayHasKey('1', $routes);

        $this->assertEquals('first', $routes[0]['name']);
        $this->assertEquals('/^\/first$/', $routes[0]['route']);

        $this->assertEquals('second', $routes[1]['name']);
        $this->assertEquals('/^\/second$/', $routes[1]['route']);
    }
}
