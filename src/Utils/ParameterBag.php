<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Parameter bag class.
 *
 * @package   rabbit-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Rabbit\Utils;

use Rabbit\Contracts\ParameterBagInterface;

/**
 * Agnostic implementation of the Symfony HttpFoundation ParameterBag.
 */
class ParameterBag implements ParameterBagInterface {

	/**
	 * @var array
	 */
	private $parameters;

	/**
	 * Constructor
	 *
	 * @param array $parameters
	 */
	public function __construct( array $parameters = [] ) {
		$this->parameters = $parameters;
	}

	/**
	 * {@inheritdoc}
	 */
	public function add( array $parameters ) {
		$this->parameters = array_merge( $this->parameters, $parameters );
	}

	/**
	 * {@inheritdoc}
	 */
	public function all(): array {
		return $this->parameters;
	}

	/**
	 * {@inheritdoc}
	 */
	public function count() {
		return count( $this->parameters );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get( $parameter, $default = null ) {
		return $this->has( $parameter ) ? $this->parameters[ $parameter ] : $default;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator() {
		return new \ArrayIterator( $this->parameters );
	}

	/**
	 * {@inheritdoc}
	 */
	public function has( $parameter ): bool {
		return array_key_exists( $parameter, $this->parameters );
	}

	/**
	 * {@inheritdoc}
	 */
	public function keys(): array {
		return array_keys( $this->parameters );
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove( $parameter ) {
		unset( $this->parameters[ $parameter ] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function replace( array $parameters ) {
		$this->parameters = $parameters;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set( $parameter, $value = null ) {
		$this->parameters[ $parameter ] = $value;
	}
}
