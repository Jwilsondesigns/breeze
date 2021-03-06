<?php
/**
 * Breeze Framework - Conditions test case
 *
 * This file contains the {@link Breeze\Dispatcher\Tests\ConditionsTest} class.
 *
 * LICENSE
 *
 * This file is part of the Breeze Framework package and is subject to the new
 * BSD license.  For full copyright and license information, please see the
 * LICENSE file that is distributed with this package.
 *
 * @author     Jeff Welch <whatthejeff@gmail.com>
 * @category   Breeze
 * @package    Dispatcher
 * @subpackage Tests
 * @copyright  Copyright (c) 2010, Breeze Framework
 * @license    New BSD License
 * @version    $Id$
 */

namespace Breeze\Dispatcher\Tests {

    /**
     * @see Breeze\Dispatcher\Conditions
     */
    use Breeze\Dispatcher\Conditions;
    /**
     * @see Breeze\Dispatcher\PassException
     */
    use Breeze\Dispatcher\PassException;

    /**
     * The test case for the {@link Breeze\Dispatcher\Conditions} class.
     *
     * @category    Breeze
     * @package     Dispatcher
     * @subpackage  Tests
     */
    class ConditionsTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * The conditions object for testing.
         *
         * @access protected
         * @param  Breeze\Dispatcher\Conditions
         */
        protected $_conditions;

        /**
         * Sets up the test case for {@link Breeze\Dispatcher\Conditions}.
         *
         * @access public
         * @return void
         */
        public function setUp()
        {
            $this->_conditions = new Conditions();
        }

        /**
         * Tests {@link Breeze\Dispatcher\Conditions::dispatchCondition()} with an
         * invalid condition name.
         */
        public function testDispatchWithInvalidName()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'is not a valid condition.');
            $this->_conditions->dispatchCondition('invalid');
        }

        /**
         * Tests {@link Breeze\Dispatcher\Conditions::add()} with an empty name and
         * name validation.
         */
        public function testAddWithEmptyName()
        {
            $this->setExpectedException('\\InvalidArgumentException', 'You must provide a name.');
            $this->_conditions->add('', function(){});
        }

        /**
         * Tests {@link Breeze\Dispatcher\Conditions::dispatchCondition()} throws
         * {@link Breeze\Dispatcher\PassException} if a condition returns false.
         */
        public function testConditionThrowsPassExceptionIfReturnsFalse()
        {
            $this->setExpectedException('Breeze\\Dispatcher\\PassException');
            $this->_conditions->add('returns_false', function(){ return false; });
            $this->_conditions->dispatchCondition('returns_false');
        }

        /**
         * Tests {@link Breeze\Dispatcher\Conditions::dispatchCondition()} doesn't
         * throw exceptions if the condition returns true.
         */
        public function testConditionDoesntThrowPassExceptionIfReturnsTrue()
        {
            $this->_conditions->add('returns_true', function(){ return true; });
            $this->_conditions->dispatchCondition('returns_true');
        }

        /**
         * Tests that the default conditions {@link Breeze\Dispatcher\Conditions::user_agent_matches()}
         * and {@link Breeze\Dispatcher\Conditions::host_name_is()} are available
         * by default.
         */
        public function testDefaultConditions()
        {
            $this->assertSame(array('user_agent_matches','host_name_is'), array_keys($this->_conditions->all()));
        }

        /**
         * Tests {@link Breeze\Dispatcher\Conditions::user_agent_matches()} with a
         * non-matching agent will throw a {@link Breeze\Dispatcher\PassException}.
         */
        public function testUserAgentMatchesWithBadAgent()
        {
            $this->setExpectedException('Breeze\\Dispatcher\\PassException');

            $_SERVER['HTTP_USER_AGENT'] = 'test/agent 1.0';
            $this->_conditions->dispatchCondition('user_agent_matches', '/thsiwontmatch/');
            $this->fail("Expected exception PassException");
        }

        /**
         * Tests {@link Breeze\Dispatcher\Conditions::user_agent_matches()} with a
         * matching agent doesn't throw a {@link Breeze\Dispatcher\PassException}.
         */
        public function testUserAgentMatchesWithGoodAgent()
        {
            $_SERVER['HTTP_USER_AGENT'] = 'test/agent 1.0';
            $this->_conditions->dispatchCondition('user_agent_matches', '/test\/agent 1\.0/');
        }

        /**
         * Tests {@link Breeze\Dispatcher\Conditions::host_name_is()} with a
         * non-matching host name will throw a {@link Breeze\Dispatcher\PassException}.
         */
        public function testHostNameIsWithBadHostName()
        {
            $this->setExpectedException('Breeze\\Dispatcher\\PassException');
            $_SERVER['HTTP_HOST'] = 'www.test.com';
            $this->_conditions->dispatchCondition('host_name_is', 'www.wontmatch.com');
        }

        /**
         * Tests {@link Breeze\Dispatcher\Conditions::host_name_is()} with a
         * matching host name doesn't throw a {@link Breeze\Dispatcher\PassException}.
         */
        public function testHostNameIsWithGoodHostName()
        {
            $_SERVER['HTTP_HOST'] = 'www.test.com';
            $this->_conditions->dispatchCondition('host_name_is', 'www.test.com');
        }
    }
}