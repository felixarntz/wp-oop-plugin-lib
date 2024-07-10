<?php
/**
 * Class Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes\Test_Case
 *
 * @package wp-oop-plugin-lib
 */

namespace Felix_Arntz\WP_OOP_Plugin_Lib\PHPUnit\Includes;

use ReflectionMethod;
use ReflectionProperty;
use WP_UnitTestCase;

/**
 * Basic class for unit tests of the plugin.
 */
abstract class Test_Case extends WP_UnitTestCase {

	/**
	 * Asserts that the given hook has one or more actions added.
	 *
	 * @param string $hook_name Action hook name.
	 * @param string $message   Optional. Message to display when the assertion fails.
	 */
	public function assertHasAction( $hook_name, $message = '' ) {
		if ( ! $message ) {
			$message = sprintf( 'Failed asserting that any action is added to the %s hook.', $hook_name );
		}
		$this->assertTrue( has_action( $hook_name ), $message );
	}

	/**
	 * Asserts that the given hook has no actions added.
	 *
	 * @param string $hook_name Action hook name.
	 * @param string $message   Optional. Message to display when the assertion fails.
	 */
	public function assertNotHasAction( $hook_name, $message = '' ) {
		if ( ! $message ) {
			$message = sprintf( 'Failed asserting that no action is added to the %s hook.', $hook_name );
		}
		$this->assertFalse( has_action( $hook_name ), $message );
	}

	/**
	 * Asserts that the given hook has one or more filters added.
	 *
	 * @param string $hook_name Filter hook name.
	 * @param string $message   Optional. Message to display when the assertion fails.
	 */
	public function assertHasFilter( $hook_name, $message = '' ) {
		if ( ! $message ) {
			$message = sprintf( 'Failed asserting that any filter is added to the %s hook.', $hook_name );
		}
		$this->assertTrue( has_filter( $hook_name ), $message );
	}

	/**
	 * Asserts that the given hook has no filters added.
	 *
	 * @param string $hook_name Filter hook name.
	 * @param string $message   Optional. Message to display when the assertion fails.
	 */
	public function assertNotHasFilter( $hook_name, $message = '' ) {
		if ( ! $message ) {
			$message = sprintf( 'Failed asserting that no filter is added to the %s hook.', $hook_name );
		}
		$this->assertFalse( has_filter( $hook_name ), $message );
	}

	/**
	 * Gets a value of a class instance property that is protected or private.
	 *
	 * @param object $instance      Class instance.
	 * @param string $property_name Property name to get value for.
	 * @return mixed Value of the property.
	 */
	protected function get_hidden_property_value( $instance, string $property_name ) {
		$prop = new ReflectionProperty( $instance, $property_name );

		$prop->setAccessible( true );
		$value = $prop->getValue( $instance );
		$prop->setAccessible( false );

		return $value;
	}

	/**
	 * Sets a value of a class instance property that is protected or private.
	 *
	 * @param object $instance      Class instance.
	 * @param string $property_name Property name to set value for.
	 * @param mixed  $value         Value to set.
	 */
	protected function set_hidden_property_value( $instance, string $property_name, $value ) {
		$prop = new ReflectionProperty( $instance, $property_name );

		$prop->setAccessible( true );
		$prop->setValue( $instance, $value );
		$prop->setAccessible( false );
	}

	/**
	 * Invokes a method of a class instance that is protected or private.
	 *
	 * @param object $instance         Class instance.
	 * @param string $method_name      Method name to invoke.
	 * @param mixed  ...$method_params Optional method parameters.
	 * @return mixed Return value of the function, unless void.
	 */
	protected function invoke_hidden_method( $instance, string $method_name, ...$method_params ) {
		$method = new ReflectionMethod( $instance, $method_name );

		$method->setAccessible( true );
		$result = $method->invoke( $instance, ...$method_params );
		$method->setAccessible( false );

		return $result;
	}
}
