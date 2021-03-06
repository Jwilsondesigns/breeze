<?php
/**
 * Breeze Framework - Errors test case
 *
 * This file contains the {@link Breeze\Errors\Tests\ErrorsTest} class.
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @category   Breeze
 * @package    Errors
 * @subpackage Tests
 * @copyright  Copyright (c) 2010, Breeze Framework
 * @license    New BSD License
 * @version    $Id$
 */

namespace Breeze\Errors\Tests {

    /**
     * @see Breeze\Errors\Errors
     */
    use Breeze\Errors\Errors;

    /**
     * The test case for the {@link Breeze\Errors\Errors} class.
     *
     * @category    Breeze
     * @package     Errors
     * @subpackage  Tests
     */
    class ErrorsTest extends \PHPUnit_Extensions_OutputTestCase
    {
        /**
         * The standard HTTP error responses.
         */
        const HTTP_400 = 'Bad Request';
        const HTTP_401 = 'Unauthorized';
        const HTTP_402 = 'Payment Required';
        const HTTP_403 = 'Forbidden';
        const HTTP_404 = 'Not Found';
        const HTTP_405 = 'Method Not Allowed';
        const HTTP_406 = 'Not Acceptable';
        const HTTP_407 = 'Proxy Authentication Required';
        const HTTP_408 = 'Request Timeout';
        const HTTP_409 = 'Conflict';
        const HTTP_410 = 'Gone';
        const HTTP_411 = 'Length Required';
        const HTTP_412 = 'Precondition Failed';
        const HTTP_413 = 'Request Entity Too Large';
        const HTTP_414 = 'Request-URI Too Long';
        const HTTP_415 = 'Unsupported Media Type';
        const HTTP_416 = 'Requested Range Not Satisfiable';
        const HTTP_417 = 'Expectation Failed';
        const HTTP_500 = 'Internal Server Error';
        const HTTP_501 = 'Not Implemented';
        const HTTP_502 = 'Bad Gateway';
        const HTTP_503 = 'Service Unavailable';
        const HTTP_504 = 'Gateway Timeout';
        const HTTP_505 = 'HTTP Version Not Supported';

        /**
         * The errors object for testing.
         *
         * @access protected
         * @param  Breeze\Errors\Errors
         */
        protected $_errors;
        /**
         * The application stub for testing {@link Breeze\Errors\Errors}.
         *
         * @access protected
         * @param  Breeze\Application
         */
        protected $_application;
        /**
         * An exception for testing {@link Breeze\Errors\Errors}.
         *
         * @access protected
         * @param  Exception
         */
        protected $_exception;

        /**
         * A sample closure to add to tests.
         *
         * @access protected
         * @param  Closure
         */
        protected $_closure;

        /**
         * Sets up the test case for {@link Breeze\Errors\Errors}.
         *
         * @access public
         * @return void
         */
        public function setUp()
        {
            $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.0';

            $this->_application = $this->getMock('Breeze\\Application', array(), array(), '', FALSE);
            $this->_exception = new \Exception('test', 403);

            $this->_closure = function(){
                echo 'Error Test';
            };

            $this->_errors = new Errors($this->_application);
            $this->_errors->setExit(false);
        }

        /**
         * Tests default error codes with {@link Breeze\Errors\Errors::getErrorForCode()}.
         */
        public function testDefinedErrorCodes()
        {
            foreach (self::_getCodes() as $code => $message) {
                $this->assertSame($this->_errors->getErrorForCode(substr($code, 5)), $message);
            }
        }

        /**
         * Tests {@link Breeze\Errors\Errors::add()} to add a default error
         * handler.
         */
        public function testSettingDefaultHandler()
        {
            $this->_errors->add($this->_closure);
            $this->_testErrorOutput();
        }

        /**
         * Tests {@link Breeze\Errors\Errors::add()} to add a handler for an
         * HTTP code.
         */
        public function testAddWithNumberName()
        {
            $this->_errors->add('403', $this->_closure);
            $this->_testErrorOutput();
        }

        /**
         * Tests {@link Breeze\Errors\Errors::add()} to add a handler for an
         * array of HTTP codes.
         */
        public function testAddWithNumberArray()
        {
            $this->_errors->add(range(400,404), $this->_closure);
            $this->_testErrorOutput();
        }

        /**
         * Tests {@link Breeze\Errors\Errors::add()} to add a handler for an
         * exception.
         */
        public function testAddWithExceptionName()
        {
            $this->_errors->add('Exception', $this->_closure);
            $this->_testErrorOutput();
        }

        /**
         * Tests {@link Breeze\Errors\Errors::add()} to add a handler for an
         * array of exceptions.
         */
        public function testAddWithExceptionArray()
        {
            $this->_errors->add(array('InvalidArgumentException','Exception'), $this->_closure);
            $this->_testErrorOutput();
        }

        /**
         * Tests {@link Breeze\Errors\Errors::add()} with an empty name.
         */
        public function testAddWithStringEmptyName()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'You must provide a name.');
            $this->_errors->add('', $this->_closure);
        }

        /**
         * Tests {@link Breeze\Errors\Errors::add()} with an empty array of
         * names.
         */
        public function testAddWithArrayEmptyName()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'You must provide a name.');
            $this->_errors->add(array(''), $this->_closure);
        }

        /**
         * Tests {@link Breeze\Errors\Errors::dispatchError()} with an invalid
         * error.
         */
        public function testDispatchWithInvalidError()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'Errors must be a string or a valid Exception');
            $this->_errors->dispatchError(new \StdClass());
        }

        /**
         * Tests {@link Breeze\Errors\Errors::dispatchError()} to dispatch an
         * error with a string.
         */
        public function testDispatchWithString()
        {
            $this->expectOutputString('Error Test');
            $this->_errors->add($this->_closure);
            $this->_errors->dispatchError('test', 403);
        }

        /**
         * Tests {@link Breeze\Errors\Errors::dispatchError()} to dispatch a
         * default handler with no layout and no backtrace.
         */
        public function testDefaultHandlerWithNoLayoutAndNoBacktrace()
        {
            $this->expectOutputString('<!DOCTYPE html><html><head><title>An error occurred</title></head><body><h1>test</h1></body></html>');
            $this->_errors->dispatchError($this->_exception);
        }

        /**
         * Tests {@link Breeze\Errors\Errors::dispatchError()} to dispatch a
         * default handler with no layout and a backtrace.
         */
        public function testDefaultHandlerWithNoLayoutAndBacktrace()
        {
            $this->_application->expects($this->once())
                               ->method('config')
                               ->will($this->returnValue(true));
            $this->_testErrorOutput(sprintf('<!DOCTYPE html><html><head><title>An error occurred</title></head><body><h1>test</h1><pre><code>%s</code></pre></body></html>', $this->_exception->getTraceAsString()));
        }

        /**
         * Tests {@link Breeze\Errors\Errors::dispatchError()} to dispatch a
         * default handler with a layout and no backtrace.
         */
        public function testDefaultHandlerWithLayoutAndNoBacktrace()
        {
            $this->_application->expects($this->at(1))
                               ->method('__call')
                               ->with($this->equalTo('layoutExists'))
                               ->will($this->returnValue(true));
            $this->_application->expects($this->at(2))
                               ->method('__call')
                               ->with($this->equalTo('fetchLayout'), $this->equalTo(array('<h1>test</h1>')))
                               ->will($this->returnValue("<mylayout><h1>test</h1></mylayout>"));
            $this->_testErrorOutput('<mylayout><h1>test</h1></mylayout>');
        }

        /**
         * Tests {@link Breeze\Errors\Errors::dispatchError()} to dispatch a
         * default handler with a layout and a backtrace.
         */
        public function testDefaultHandlerWithLayoutAndBacktrace()
        {
            $contents = sprintf('<h1>test</h1><pre><code>%s</code></pre>', $this->_exception->getTraceAsString());

            $this->_application->expects($this->once())
                               ->method('config')
                               ->will($this->returnValue(true));
            $this->_application->expects($this->at(1))
                               ->method('__call')
                               ->with($this->equalTo('layoutExists'))
                               ->will($this->returnValue(true));
            $this->_application->expects($this->at(2))
                               ->method('__call')
                               ->with($this->equalTo('fetchLayout'), $this->equalTo(array($contents)))
                               ->will($this->returnValue("<mylayout>$contents</mylayout>"));

            $this->_testErrorOutput("<mylayout>$contents</mylayout>");
        }

        /**
         * Tests {@link Breeze\Errors\Errors::setExit()} changes the exit
         * option.
         */
        public function testExit()
        {
            $this->assertFalse($this->_errors->getExit());
            $this->_errors->setExit(true);
            $this->assertTrue($this->_errors->getExit());
        }

        /**
         * Tests errors issue the correct status headers.
         */
        public function testErrorsIssueCorrectStatusHeader()
        {
            $this->markTestSkipped(
              "At the moment it's not possible to test HTTP status codes.  Xdebug offers xdebug_get_headers, but it doesn't check status codes.  See: http://bugs.xdebug.org/view.php?id=601"
            );
        }

        /**
         * Gets an array of the defined HTTP error constants.
         *
         * @access protected
         * @return array
         * @static
         */
        protected static function _getCodes() {
            $codes = array();

            $class = new \ReflectionClass(get_class());
            foreach ($class->getConstants() as $name => $constant) {
                if (substr($name, 0, 4) == 'HTTP') {
                    $codes[$name] = $constant;
                }
            }

            return $codes;
        }

        /**
         * Tests that the output from {@link Breeze\Errors\Errors::dispatchError()}
         * matches an expected string.
         *
         * @access protected
         * @return array
         */
        protected function _testErrorOutput($expected = 'Error Test')
        {
            $this->expectOutputString($expected);
            $this->_errors->dispatchError($this->_exception);
        }
    }
}