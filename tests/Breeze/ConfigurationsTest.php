<?php
/**
 * Breeze Framework - Configurations test case
 *
 * This file contains the {@link Breeze\Tests\ConfigurationsTest} class.
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
     * @see Breeze\Configurations
     */
    use Breeze\Configurations;

    /**
     * The test case for the {@link Breeze\Configurations} class.
     *
     * @category    Breeze
     * @package     Application
     * @subpackage  Tests
     */
    class ConfigurationsTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * The configurations object for testing.
         *
         * @access protected
         * @param  Breeze\Configurations
         */
        protected $_configurations;

        /**
         * The list default configuration settings.
         *
         * @access protected
         * @param  array
         */
        protected $_defaults = array(
            'template_engine'       => 'PHP',
            'template_options'      => array(),
            'template_directory'    => '../views',
            'template_extension'    => '.php',
            'template_layout'       => 'layout',
            'application_variable'  => 'breeze',
            'errors_backtrace'      => true
        );

        /**
         * Sets up the test case for {@link Breeze\Configurations}.
         *
         * @access public
         * @return void
         */
        public function setUp()
        {
            $this->_configurations = new Configurations();
        }

        /**
         * Tests {@link Breeze\Configurations::get()} with an unset key.
         */
        public function testGetWithUnsetKey()
        {
            $this->assertNull($this->_configurations->get('unset key'));
        }

        /**
         * Tests {@link Breeze\Configurations::get()} with a set key.
         */
        public function testGetWithSetKey()
        {
            $this->_configurations->set('a key', 'a value');
            $this->assertSame('a value', $this->_configurations->get('a key'));
        }

        /**
         * Tests the default configurations for {@link Breeze\Configurations}.
         */
        public function testDefaults()
        {
            foreach ($this->_defaults as $key => $default) {
                $this->assertSame($default, $this->_configurations->get($key));
            }
        }

        /**
         * Tests overriding the defaults in the {@link Breeze\Configurations::__construct()}.
         */
        public function testOverriddingDefaultsInConstructor()
        {
            $new_values = array(
                'template_engine'=>'smarty',
                'template_extension'=>'.tpl'
            );

            $this->_configurations = new Configurations($new_values);

            foreach (array_merge($this->_defaults, $new_values) as $key => $default) {
                $this->assertSame($default, $this->_configurations->get($key));
            }
        }

        /**
         * Tests {@link Breeze\Configurations::set()} with a new value.
         */
        public function testSetWithStringAndNewKey()
        {
            $this->_configurations->set('a key', 'a value');
            $this->assertSame('a value', $this->_configurations->get('a key'));
        }

        /**
         * Tests {@link Breeze\Configurations::set()} with an existing value.
         */
        public function testSetWithStringAndExistingKey()
        {
            $this->_configurations->set('template_engine', 'smarty');
            $this->assertSame('smarty', $this->_configurations->get('template_engine'));
        }

        /**
         * Tests {@link Breeze\Configurations::set()} with an array and new values.
         */
        public function testSetWithArrayAndNewKeys()
        {
            $new_values = array(
                'a key1' => 'a value1',
                'a key2' => 'a value2'
            );
            $this->_configurations->set($new_values);

            foreach ($new_values as $key => $value) {
                $this->assertSame($value, $this->_configurations->get($key));
            }
        }

        /**
         * Tests {@link Breeze\Configurations::set()} with an array and existing
         * values.
         */
        public function testSetWithArrayAndExistingKeys()
        {
            $new_values = array(
                'a key1' => 'a value1',
                'a key2' => 'a value2',
                'template_engine'=>'smarty',
                'template_extension'=>'.tpl'
            );
            $this->_configurations->set($new_values);

            foreach ($new_values as $key => $value) {
                $this->assertSame($value, $this->_configurations->get($key));
            }
        }
    }

}