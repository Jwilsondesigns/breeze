<?php
/**
 * Breeze Framework - Application test case
 *
 * This file contains the {@link Breeze\Tests\ApplicationTest} class.
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @category   Breeze
 * @package    Application
 * @subpackage Tests
 * @copyright  Copyright (c) 2010, Breeze Framework
 * @license    New BSD License
 * @version    $Id$
 */

namespace Breeze\Tests {

    /**
     * @see Breeze\Application
     */
    use Breeze\Application;

    /**
     * The test case for the {@link Breeze\Application} class.
     *
     * @category   Breeze
     * @package    Application
     * @subpackage Tests
     */
    class ApplicationTest extends \PHPUnit_Extensions_OutputTestCase
    {
        /**
         * The application object for testing.
         *
         * @access protected
         * @param  Breeze\Application
         */
        protected $_application;
        /**
         * The application configurations for {@link Breeze\Tests\ApplicationTest::$_application}
         * that will hold the mocked dependencies.
         *
         * @access protected
         * @param  Breeze\Configurations
         */
        protected $_configurations;
        /**
         * The mocked dependencies for {@link Breeze\Tests\ApplicationTest::$_application}.
         *
         * @access public
         * @param  array
         */
        protected $_mocks = array();

        /**
         * Sets up the test case for {@link Breeze\Application}.
         *
         * @access public
         * @return void
         */
        public function setUp()
        {
            $this->_setupMockedDependencies();
            $this->_mockApplication();
        }

        /**
         * Tests {@link Breeze\Application::pass()} throws {@link Breeze\Dispatcher\PassException}.
         */
        public function testPass()
        {
            $this->setExpectedException('Breeze\\Dispatcher\\PassException');
            $this->_application->pass();
        }

        /**
         * Tests {@link Breeze\Application::redirect()} throws a location header.
         */
        public function testRedirect()
        {
            $this->_application->redirect('http://www.breezephp.com/', null, false);
            $this->assertSame(xdebug_get_headers(), array('Location: http://www.breezephp.com/'));
        }

        /**
         * Tests {@link Breeze\Application::redirect()} with a custom status code.
         */
        public function testRedirectWithDifferentCode()
        {
            $this->markTestSkipped(
              "At the moment it's not possible to test HTTP status codes.  Xdebug offers xdebug_get_headers, but it doesn't check status codes.  See: http://bugs.xdebug.org/view.php?id=601"
            );
        }

        /**
         * Tests {@link Breeze\Application::config()} to set a single value.
         */
        public function testConfigSetWithSingleValue()
        {
            $this->_configurations->expects($this->once())
                                  ->method('set')
                                  ->with($this->equalTo('this is a'), $this->equalTo('test'));
            $this->_application->config('this is a', 'test');
        }

        /**
         * Tests {@link Breeze\Application::config()} to set values with an array.
         */
        public function testConfigSetWithArray()
        {
            $config = array(
                'test1' => 'value1',
                'test2' => 'value2'
            );

            $this->_configurations->expects($this->once())
                                  ->method('set')
                                  ->with($this->equalTo($config));
            $this->_application->config($config);
        }

        /**
         * Tests {@link Breeze\Application::config()} to retrieve a value.
         */
        public function testConfigGet()
        {
            $this->_configurations->expects($this->at(0))
                                  ->method('get')
                                  ->with($this->equalTo('this is a'))
                                  ->will($this->returnValue('test'));
            $this->assertSame('test', $this->_application->config('this is a'));
        }

        /**
         * Tests {@link Breeze\Application::condition()} to retrieve a condition without parameters.
         */
        public function testConditionGetWithoutParameters()
        {
           $this->_mocks['conditions_object']->expects($this->once())
                                             ->method('dispatchCondition')
                                             ->with($this->equalTo('test'));
           $this->_application->condition('test');
        }

        /**
         * Tests {@link Breeze\Application::condition()} to retrieve a condition with parameters.
         */
        public function testConditionGetWithParameters()
        {
           $this->_mocks['conditions_object']->expects($this->once())
                                             ->method('dispatchCondition')
                                             ->with($this->equalTo('test'), $this->equalTo('param1'), $this->equalTo('param2'));
           $this->_application->condition('test', 'param1', 'param2');
        }

        /**
         * Tests {@link Breeze\Application::condition()} to set a condition.
         */
        public function testConditionSet()
        {
           $closure = function(){};
           $this->_mocks['conditions_object']->expects($this->once())
                                             ->method('add')
                                             ->with($this->equalTo('test'), $this->equalTo($closure));
           $this->_application->condition('test', $closure);
        }

        /**
         * Tests {@link Breeze\Application::template()} to get a template variable.
         */
        public function testTemplateGet()
        {
            $this->_mocks['view_object']->expects($this->once())
                                        ->method('__get')
                                        ->with($this->equalTo('this_is_a'))
                                        ->will($this->returnValue('test'));
            $this->assertSame('test', $this->_application->template('this_is_a'));
        }

        /**
         * Tests {@link Breeze\Application::template()} to set a single template variable.
         */
        public function testTemplateSetSingle()
        {
            $this->_mocks['view_object']->expects($this->once())
                                        ->method('__set')
                                        ->with($this->equalTo('this_is_a'), $this->equalTo('value'));
            $this->_application->template('this_is_a', 'value');
        }

        /**
         * Tests {@link Breeze\Application::template()} to set multiple template variables
         * with an array.
         */
        public function testTemplateSetArray()
        {
            $template_variables = array(
                'test1' => 'value1',
                'test2' => 'value2'
            );

            $this->_mocks['view_object']->expects($this->once())
                                        ->method('addVariables')
                                        ->with($this->equalTo($template_variables));
            $this->_application->template($template_variables);
        }

        /**
         * Tests {@link Breeze\Application::error()} to dispatch an error using a standard
         * HTTP error code.
         */
        public function testErrorDispatchDefaultHttpErrorCode()
        {
            $this->_mocks['errors_object']->expects($this->once())
                                          ->method('getErrorForCode')
                                          ->with($this->equalTo(404))
                                          ->will($this->returnValue('Page Not Found'));
            $this->_mocks['errors_object']->expects($this->once())
                                          ->method('dispatchError')
                                          ->with($this->equalTo('404 - Page Not Found'), $this->equalTo('404'));
            $this->_application->error(404);
        }

        /**
         * Tests {@link Breeze\Application::error()} to dispatch an error using a non-standard
         * HTTP error code.
         */
        public function testErrorDispatchGenericMessageWithNonHttpErrorCode()
        {
            $this->_mocks['errors_object']->expects($this->once())
                                          ->method('getErrorForCode')
                                          ->with($this->equalTo(600))
                                          ->will($this->returnValue(null));
            $this->_mocks['errors_object']->expects($this->once())
                                          ->method('dispatchError')
                                          ->with($this->equalTo('An Error Occurred.'), $this->equalTo('600'));
            $this->_application->error(600);
        }

        /**
         * Tests {@link Breeze\Application::error()} to dispatch a custom error message.
         */
        public function testErrorDispatchCustomMessage()
        {
            $this->_mocks['errors_object']->expects($this->once())
                                          ->method('dispatchError')
                                          ->with($this->equalTo('this is a message'), $this->equalTo('0'));
            $this->_application->error("this is a message");
        }

        /**
         * Tests {@link Breeze\Application::error()} to add a default error.
         */
        public function testErrorAddDefault()
        {
            $closure = function(){};
            $this->_mocks['errors_object']->expects($this->once())
                                          ->method('add')
                                          ->with($this->equalTo($closure));
            $this->_application->error($closure);
        }

        /**
         * Tests {@link Breeze\Application::error()} to add an error using a code.
         */
        public function testErrorAddCode()
        {
            $closure = function(){};
            $this->_mocks['errors_object']->expects($this->once())
                                          ->method('add')
                                          ->with($this->equalTo(403), $this->equalTo($closure));
            $this->_application->error(403, $closure);
        }

        /**
         * Tests {@link Breeze\Application::error()} to add an error using an exception.
         */
        public function testErrorAddException()
        {
            $closure = function(){};
            $this->_mocks['errors_object']->expects($this->once())
                                          ->method('add')
                                          ->with($this->equalTo('Exception'), $this->equalTo($closure));
            $this->_application->error('Exception', $closure);
        }

        /**
         * Tests {@link Breeze\Application::error()} to add errors using an array.
         */
        public function testErrorAddArray()
        {
            $closure = function(){};
            $range = range(400, 500);
            $this->_mocks['errors_object']->expects($this->once())
                                          ->method('add')
                                          ->with($this->equalTo($range), $this->equalTo($closure));
            $this->_application->error($range, $closure);
        }

        /**
         * Tests calling an unknown method triggers an undefined function method.
         */
        public function testCallUnkownMethod()
        {
            $this->setExpectedException('\\PHPUnit_Framework_Error', 'Call to undefined function:');
            $this->_application->this_will_fail();
        }

        /**
         * Tests {@link Breeze\Application::display()} to display a template with no variables.
         */
        public function testViewDisplay()
        {
            $this->_mocks['view_object']->expects($this->once())
                                        ->method('display')
                                        ->with($this->equalTo('template'));
            $this->_application->display('template');
        }

        /**
         * Tests {@link Breeze\Application::display()} to display a template with variables.
         */
        public function testViewDisplayWithVariables()
        {
            $template_variables = array('this is a', 'test');
            $this->_mocks['view_object']->expects($this->once())
                                        ->method('display')
                                        ->with($this->equalTo('template'), $this->equalTo($template_variables));
            $this->_application->display('template', $template_variables);
        }

        /**
         * Tests {@link Breeze\Application::fetch()} to get the contents of a template.
         */
        public function testFetch()
        {
            $this->_mocks['view_object']->expects($this->once())
                                        ->method('fetch')
                                        ->with($this->equalTo('template'))
                                        ->will($this->returnValue('template contents'));
            $this->assertSame('template contents', $this->_application->fetch('template'));
        }

        /**
         * Tests {@link Breeze\Application::fetch()} to get the contents of a template with variables.
         */
        public function testViewFetchWithVariables()
        {
            $template_variables = array('this is a', 'test');
            $this->_mocks['view_object']->expects($this->once())
                                        ->method('fetch')
                                        ->with($this->equalTo('template'), $this->equalTo($template_variables))
                                        ->will($this->returnValue('template contents'));
            $this->assertSame('template contents', $this->_application->fetch('template', $template_variables));
        }

        /**
         * Tests {@link Breeze\Application::layout()} to set the layout file.
         */
        public function testViewLayout()
        {
            $this->_mocks['view_object']->expects($this->once())
                                        ->method('layout')
                                        ->with($this->equalTo('layout'));
            $this->_application->layout('layout');
        }

        /**
         * Tests {@link Breeze\Application::partial()} to fetch the contents of a partial.
         */
        public function testPartial()
        {
            $this->_mocks['view_object']->expects($this->once())
                                        ->method('partial')
                                        ->with($this->equalTo('template'))
                                        ->will($this->returnValue('template contents'));
            $this->assertSame('template contents', $this->_application->partial('template'));
        }

        /**
         * Tests {@link Breeze\Application::partial()} to fetch the contents of a partial.
         * with variables.
         */
        public function testViewPartialWithVariables()
        {
            $template_variables = array('this is a', 'test');
            $this->_mocks['view_object']->expects($this->once())
                                        ->method('partial')
                                        ->with($this->equalTo('template'), $this->equalTo($template_variables))
                                        ->will($this->returnValue('template contents'));
            $this->assertSame('template contents', $this->_application->partial('template', $template_variables));
        }

        /**
         * Tests {@link Breeze\Application::layoutExists()} to see if a layout exists.
         */
        public function testViewLayoutExists()
        {
            $this->_mocks['view_object']->expects($this->once())
                                        ->method('layoutExists')
                                        ->will($this->returnValue(true));
            $this->assertTrue($this->_application->layoutExists());
        }

        /**
         * Tests {@link Breeze\Application::fetchLayout()} to wrap contents in a layout file.
         */
        public function testViewFetchLayout()
        {
            $this->_mocks['view_object']->expects($this->once())
                                        ->method('fetchLayout')
                                        ->with($this->equalTo('contents'))
                                        ->will($this->returnValue('<layout>contents</layout>'));
            $this->assertSame('<layout>contents</layout>', $this->_application->fetchLayout('contents'));
        }

        /**
         * Tests {@link Breeze\Application::get()}, {@link Breeze\Application::post()},
         * {@link Breeze\Application::put()}, {@link Breeze\Application::delete()} to dispatch
         * synthetic requests.
         */
        public function testDispatcherSyntheticRequest()
        {
            foreach (array('get','post','put','delete') as $method) {
                $this->_mocks['dispatcher_object']->expects($this->at(0))
                                                  ->method('dispatch')
                                                  ->with($this->equalTo($method), $this->equalTo('/my/page'));
                $this->_application->$method('/my/page');
            }
        }

        /**
         * Tests {@link Breeze\Application::get()}, {@link Breeze\Application::post()},
         * {@link Breeze\Application::put()}, {@link Breeze\Application::delete()} to add
         * new routes.
         */
        public function testDispatcherAdd()
        {
            $closure = function(){};
            foreach (array('get','post','put','delete') as $method) {
                $this->_mocks['dispatcher_object']->expects($this->at(0))
                                                  ->method('__call')
                                                  ->with($this->equalTo($method), $this->equalTo(array('/my/page', $closure)));
                $this->_application->$method('/my/page', $closure);
            }
        }

        /**
         * Tests {@link Breeze\Application::__set()} to set a template variable.
         */
        public function testSet()
        {
            $this->_mocks['view_object']->expects($this->once())
                                        ->method('__set')
                                        ->with($this->equalTo('this_is_a'), $this->equalTo('test'));
            $this->_application->this_is_a = 'test';
        }

        /**
         * Tests {@link Breeze\Application::__get()} to get a template variable.
         */
        public function testGet()
        {
            $this->_mocks['view_object']->expects($this->once())
                                        ->method('__get')
                                        ->with($this->equalTo('this_is_a'))
                                        ->will($this->returnValue('test'));
            $this->assertSame('test', $this->_application->this_is_a);
        }

        /**
         * Tests {@link Breeze\Application::__isset()} to check if a template variable is set.
         */
        public function testIsset()
        {
            $this->_mocks['view_object']->expects($this->once())
                                        ->method('__isset')
                                        ->with($this->equalTo('this_is_a'))
                                        ->will($this->returnValue(false));
            $this->assertFalse(isset($this->_application->this_is_a));
        }

        /**
         * Tests {@link Breeze\Application::__unset()} to unset a template variable.
         */
        public function testUnset()
        {
            $this->_mocks['view_object']->expects($this->once())
                                        ->method('__unset')
                                        ->with($this->equalTo('this_is_a'));
            unset($this->_application->this_is_a);
        }

        /**
         * Tests {@link Breeze\Application::helper()} to add a helper.
         */
        public function testAddHelper()
        {
            $closure = function(){};
            $this->_mocks['helpers_object']->expects($this->once())
                                           ->method('add')
                                           ->with($this->equalTo('name'), $this->equalTo($closure), 'label');
            $this->_application->helper('name', $closure);
        }

        /**
         * Tests {@link Breeze\Application::helper()} to add a helper.
         */
        public function testRunHelper()
        {
            $this->_mocks['helpers_object']->expects($this->once())
                                           ->method('has')
                                           ->with('test_helper')
                                           ->will($this->returnValue(true));
            $this->_mocks['helpers_object']->expects($this->once())
                                           ->method('get')
                                           ->with('test_helper')
                                           ->will($this->returnValue(function($name){ return "hello $name"; }));
            $this->assertSame('hello test', $this->_application->test_helper('test'));
        }

        /**
         * Tests {@link Breeze\Application::getHelpers()} to get a list of defined helpers.
         */
        public function testGetHelpers()
        {
            $helpers = array(
                'test1','test2','get','delete','put','post','any','before','after','config',
                'template','display','fetch','pass','helper','run','error','condition','redirect',
                'partial'
            );
            $this->_mocks['helpers_object']->expects($this->once())
                                           ->method('all')
                                           ->will($this->returnValue(array('test1'=>function(){}, 'test2'=>function(){})));
            $this->assertSame($helpers, $this->_application->getHelpers());
        }

        /**
         * Tests {@link Breeze\Application::before()} to add a before filter.
         */
        public function testAddBeforeFilter()
        {
            $closure = function(){};
            $this->_mocks['before_filters_object']->expects($this->once())
                                                  ->method('add')
                                                  ->with($this->equalTo($closure));
            $this->_application->before($closure);
        }

        /**
         * Tests {@link Breeze\Application::after()} to add an after filter.
         */
        public function testAddAfterFilter()
        {
            $closure = function(){};
            $this->_mocks['after_filters_object']->expects($this->once())
                                                 ->method('add')
                                                 ->with($this->equalTo($closure));
            $this->_application->after($closure);
        }

        /**
         * Tests {@link Breeze\Application::filter()} with a bad filter type throws an
         * InvalidArgumentException.
         */
        public function testFilterWithBadType()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'is not a valid filter type.');
            $this->_application->filter('DOES NOT EXIST');
        }

        /**
         * Tests {@link Breeze\Application::filter()} to run before filters.
         */
        public function testFilterBefore()
        {
            $this->expectOutputString('test1test2');
            $this->_mocks['before_filters_object']->expects($this->once())
                                                  ->method('all')
                                                  ->will($this->returnValue(array('test1'=>function(){ echo "test1"; }, 'test2'=>function(){ echo "test2"; })));
            $this->_application->filter('before');
        }

        /**
         * Tests {@link Breeze\Application::filter()} to run after filters.
         */
        public function testFilterAfter()
        {
            $this->expectOutputString('test1test2');
            $this->_mocks['after_filters_object']->expects($this->once())
                                                 ->method('all')
                                                 ->will($this->returnValue(array('test1'=>function(){ echo "test1"; }, 'test2'=>function(){ echo "test2"; })));
            $this->_application->filter('after');
        }

        /**
         * Tests {@link Breeze\Application::run()} to dispatch exceptions and filters.
         */
        public function testRun()
        {
            $this->expectOutputString('before1before2contentsafter1after2');
            $this->_mocks['before_filters_object']->expects($this->once())
                                                  ->method('all')
                                                  ->will($this->returnValue(array('test1'=>function(){ echo "before1"; }, 'test2'=>function(){ echo "before2"; })));
            $this->_mocks['after_filters_object']->expects($this->once())
                                                 ->method('all')
                                                 ->will($this->returnValue(array('test1'=>function(){ echo "after1"; }, 'test2'=>function(){ echo "after2"; })));
            $this->_mocks['dispatcher_object']->expects($this->once())
                                              ->method('dispatch')
                                              ->will($this->returnCallback(function(){
                                                  echo "contents";
                                              }));
            $this->_application->run();
        }

        /**
         * Tests exceptions throw from {@link Breeze\Dispatcher\Dispatcher::dispatch()} in
         * {@link Breeze\Application::run()} will be dispatched using {@link Breeze\\Errors\\Errors}
         */
        public function testRunWithExceptions()
        {
            $exception = new \Exception('Something bad happened');
            $this->_mocks['dispatcher_object']->expects($this->once())
                                              ->method('dispatch')
                                              ->will($this->throwException($exception));
            $this->_mocks['errors_object']->expects($this->once())
                                          ->method('dispatchError')
                                          ->with($this->equalTo($exception));
            $this->_application->run();
        }

        /**
         * Tests injecting a bad dependency while constructing {@link Breeze\Application} throws
         * an {@link UnexpectedValueException}.
         */
        public function testInjectBadDependency()
        {
            $this->setExpectedException('\\UnexpectedValueException', 'stdClass is not an instance of Breeze\\View\\View.');
            $this->_configurations = $this->getMock('Breeze\\Configurations', array(), array(), '', FALSE);
            $this->_configurations->expects($this->any())
                                  ->method('get')
                                  ->will($this->returnValue(new \stdClass()));

            new Application($this->_configurations);
        }

        /**
         * Tests {@link Breeze\Application::register()} with an invalid closure as a plugin
         * throws an {@link UnexpectedValueException}.
         *
         */
        public function testRegisterInvalidPlugin()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'You must provide a callable PHP function.');
            Application::register('bad plugin', 'this will not work');
        }

        /**
         * Tests {@link Breeze\Application::register()} with an invalid plugin name throws
         * an {@link UnexpectedValueException}.
         *
         */
        public function testRegisterPluginWithInvalidName()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'You must provide a name.');
            Application::register('', function(){});
        }

        /**
         * Tests {@link Breeze\Application::register()} to register a plugin.
         *
         */
        public function testRegisterPlugin()
        {
            $this->expectOutputString('testing');
            Application::register('test_plugin', function(){ echo "testing"; });
            $this->_mockApplication();

            Application::unregister('test_plugin');
        }

        /**
         * Tests {@link Breeze\Application::register()} to register a plugin which registers a helper.
         *
         */
        public function testRegisterPluginWithHelper()
        {
            $closure = function(){};
            $this->_mocks['helpers_object']->expects($this->once())
                                           ->method('add')
                                           ->with($this->equalTo('name'), $this->equalTo($closure), 'label');
            Application::register('test_plugin', function($app) use ($closure){
                $app->helper('name', $closure);
            });
            $this->_mockApplication();

            Application::unregister('test_plugin');
        }

        /**
         * Sets up mocks for testing Breeze\Application.
         *
         * @access protected
         * @return void
         */
        protected function _setupMockedDependencies()
        {
            $this->_mocks['view_object'] = $this->getMock('Breeze\\View\\View', array(), array(), '', FALSE);
            $this->_mocks['errors_object'] = $this->getMock('Breeze\\Errors\\Errors', array(), array(), '', FALSE);
            $this->_mocks['dispatcher_object'] = $this->getMock('Breeze\\Dispatcher\\Dispatcher', array(), array(), '', FALSE);
            $this->_mocks['conditions_object'] = $this->getMock('Breeze\\Dispatcher\\Conditions', array(), array(), '', FALSE);
            $this->_mocks['helpers_object'] = $this->getMock('Breeze\\ClosuresCollection', array(), array(), '', FALSE);
            $this->_mocks['before_filters_object'] = $this->getMock('Breeze\\ClosuresCollection', array(), array(), '', FALSE);
            $this->_mocks['after_filters_object'] = $this->getMock('Breeze\\ClosuresCollection', array(), array(), '', FALSE);

            $this->_configurations = $this->getMock('Breeze\\Configurations', array(), array(), '', FALSE);
        }

        /**
         * Get an instance of Breeze\Application with mocked dependencies injected.
         *
         * @access protected
         * @return void
         */
        protected function _mockApplication()
        {
            $i = 0;
            foreach ($this->_mocks as $mock) {
                $this->_configurations->expects($this->at($i++))
                                      ->method('get')
                                      ->will($this->returnValue($mock));
            }
            $this->_application = new Application($this->_configurations);
        }
    }

}