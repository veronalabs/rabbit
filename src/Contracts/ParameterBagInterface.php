<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Definition of the public contract to be available on a ParameterBag instance
 *
 * @package   rabbit-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Rabbit\Contracts;

interface ParameterBagInterface extends \Countable, \IteratorAggregate {

	/**
	 * Adds a new list of parameters to the ones that are already stored in the
	 * container
	 *
	 * @param array $parameters
	 */
	public function add( array $parameters);

	/**
	 * Gets all the parameters stored in the container
	 *
	 * @return array
	 */
	public function all(): array;

	/**
	 * Gets the value of a certain parameter in the container or the default if
	 * it does not exist
	 *
	 * @param string $parameter
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public function get( $parameter, $default = null);

	/**
	 * Gets whether a certain parameter exists in the container
	 *
	 * @param string $parameter
	 *
	 * @return bool
	 */
	public function has( $parameter): bool;

	/**
	 * Gets the list of parameters that are defined in the container
	 *
	 * @return array
	 */
	public function keys(): array;

	/**
	 * Removes a parameter from the container
	 *
	 * @param string $parameter
	 */
	public function remove( $parameter);

	/**
	 * Replaces the current list of parameters with a new set
	 *
	 * @param array $parameters
	 */
	public function replace( array $parameters);

	/**
	 * Sets the value of a parameter in the container, creating it if it does not
	 * exist
	 *
	 * @param string $parameter
	 * @param mixed  $value
	 */
	public function set( $parameter, $value = null);
}
