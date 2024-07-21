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
	 * Asserts that the given hook has one or more actions added.
	 *
	 * @param string   $hook_name Action hook name.
	 * @param callable $callback  Hook callback to check for.
	 * @param string   $message   Optional. Message to display when the assertion fails.
	 */
	public function assertHasActionCallback( $hook_name, $callback, $message = '' ) {
		if ( ! $message ) {
			$message = sprintf( 'Failed asserting that %s is added to the %s action hook.', $this->get_callback_name( $callback ), $hook_name );
		}
		$this->assertTrue( (bool) has_action( $hook_name, $callback ), $message );
	}

	/**
	 * Asserts that the given hook has no actions added.
	 *
	 * @param string   $hook_name Action hook name.
	 * @param callable $callback  Hook callback to check for.
	 * @param string   $message   Optional. Message to display when the assertion fails.
	 */
	public function assertNotHasActionCallback( $hook_name, $callback, $message = '' ) {
		if ( ! $message ) {
			$message = sprintf( 'Failed asserting that %s is not added to the %s action hook.', $this->get_callback_name( $callback ), $hook_name );
		}
		$this->assertFalse( has_action( $hook_name, $callback ), $message );
	}

	/**
	 * Asserts that the given hook has one or more filters added.
	 *
	 * @param string   $hook_name Filter hook name.
	 * @param callable $callback  Hook callback to check for.
	 * @param string   $message   Optional. Message to display when the assertion fails.
	 */
	public function assertHasFilterCallback( $hook_name, $callback, $message = '' ) {
		if ( ! $message ) {
			$message = sprintf( 'Failed asserting that %s is added to the %s filter hook.', $this->get_callback_name( $callback ), $hook_name );
		}
		$this->assertTrue( (bool) has_filter( $hook_name, $callback ), $message );
	}

	/**
	 * Asserts that the given hook has no filters added.
	 *
	 * @param string   $hook_name Filter hook name.
	 * @param callable $callback  Hook callback to check for.
	 * @param string   $message   Optional. Message to display when the assertion fails.
	 */
	public function assertNotHasFilterCallback( $hook_name, $callback, $message = '' ) {
		if ( ! $message ) {
			$message = sprintf( 'Failed asserting that %s is not added to the %s filter hook.', $this->get_callback_name( $callback ), $hook_name );
		}
		$this->assertFalse( has_filter( $hook_name, $callback ), $message );
	}

	/**
	 * Expects a function to trigger a _doing_it_wrong() notice.
	 *
	 * @param string $function_name Function or method name expected to trigger the notice.
	 */
	public function expectDoingItWrong( string $function_name ) {
		$this->expected_doing_it_wrong[] = $function_name;
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

	/**
	 * Populates the given arguments with IDs from the given ID lists.
	 *
	 * This is useful to handle arguments from data provider methods as they evaluated before the dynamic test data is
	 * generated. The method will replace placeholders like 'posts::0' with the actual post ID in an ID list with the
	 * key 'posts'.
	 *
	 * @param array                $args     Arguments to populate.
	 * @param array<string, int[]> $id_lists ID lists to use for populating. Each list must be an associative array
	 *                                       with keys being the placeholder prefix and values being associative arrays
	 *                                       with the placeholder as key and the actual ID as value.
	 * @return array Populated arguments.
	 */
	protected function populate_with_ids( array $args, array $id_lists ) {
		$populated_args = array();
		foreach ( $args as $key => $value ) {
			if ( is_string( $value ) && str_contains( $value, '::' ) ) {
				list( $id_list_key, $id ) = explode( '::', $value, 2 );
				if ( isset( $id_lists[ $id_list_key ][ $id ] ) ) {
					$populated_args[ $key ] = $id_lists[ $id_list_key ][ $id ];
				} else {
					$populated_args[ $key ] = $value;
				}
			} elseif ( is_array( $value ) ) {
				$populated_args[ $key ] = $this->populate_with_ids( $value, $id_lists );
			} else {
				$populated_args[ $key ] = $value;
			}
		}
		return $populated_args;
	}

	private function get_callback_name( callable $callback ): string {
		if ( is_string( $callback ) ) {
			return $callback;
		}

		if ( is_array( $callback ) ) {
			if ( is_object( $callback[0] ) ) {
				return get_class( $callback[0] ) . '::' . $callback[1];
			}

			return $callback[0] . '::' . $callback[1];
		}

		return 'anonymous function';
	}
}
