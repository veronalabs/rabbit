<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Templates engine functions.
 *
 * @package   backyard-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Templates\Template;

use LogicException;

/**
 * Template functions registry.
 */
class Functions {

	/**
	 * Array of template functions.
	 *
	 * @var array
	 */
	protected $functions = array();

	/**
	 * Add a new template function.
	 *
	 * @param  string   $name
	 * @param  callback $callback
	 * @throws LogicException When function name is already registered.
	 * @return Functions
	 */
	public function add( $name, $callback ) {
		if ( $this->exists( $name ) ) {
			throw new LogicException(
				'The template function name "' . $name . '" is already registered.'
			);
		}

		$this->functions[ $name ] = new Func( $name, $callback );

		return $this;
	}

	/**
	 * Remove a template function.
	 *
	 * @param  string $name
	 * @throws LogicException When function name is not found.
	 * @return Functions
	 */
	public function remove( $name ) {
		if ( ! $this->exists( $name ) ) {
			throw new LogicException(
				'The template function "' . $name . '" was not found.'
			);
		}

		unset( $this->functions[ $name ] );

		return $this;
	}

	/**
	 * Get a template function.
	 *
	 * @param  string $name
	 * @throws LogicException When function name is not found.
	 * @return Func
	 */
	public function get( $name ) {
		if ( ! $this->exists( $name ) ) {
			throw new LogicException( 'The template function "' . $name . '" was not found.' );
		}

		return $this->functions[ $name ];
	}

	/**
	 * Check if a template function exists.
	 *
	 * @param  string $name
	 * @return boolean
	 */
	public function exists( $name ) {
		return isset( $this->functions[ $name ] );
	}
}
