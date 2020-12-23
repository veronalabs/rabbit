<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Templates engine function.
 *
 * @package   backyard-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Templates\Template;

use Backyard\Contracts\TemplatesEngineExtensionInterface;
use LogicException;

/**
 * Representation of a single function for the templates engine.
 */
class Func {

	/**
	 * The function name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The function callback.
	 *
	 * @var callable
	 */
	protected $callback;

	/**
	 * Create new Func instance.
	 *
	 * @param string   $name
	 * @param callable $callback
	 */
	public function __construct( $name, $callback ) {
		$this->setName( $name );
		$this->setCallback( $callback );
	}

	/**
	 * Set the function name.
	 *
	 * @param  string $name
	 * @throws LogicException When not a valid function name.
	 * @return Func
	 */
	public function setName( $name ) {
		if ( preg_match( '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $name ) !== 1 ) {
			throw new LogicException(
				'Not a valid function name.'
			);
		}

		$this->name = $name;

		return $this;
	}

	/**
	 * Get the function name.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set the function callback
	 *
	 * @param  callable $callback
	 * @throws LogicException When not a valid function callback.
	 * @return Func
	 */
	public function setCallback( $callback ) {
		if ( ! is_callable( $callback, true ) ) {
			throw new LogicException(
				'Not a valid function callback.'
			);
		}

		$this->callback = $callback;

		return $this;
	}

	/**
	 * Get the function callback.
	 *
	 * @return callable
	 */
	public function getCallback() {
		return $this->callback;
	}

	/**
	 * Call the function.
	 *
	 * @param  Template $template
	 * @param  array    $arguments
	 * @return mixed
	 */
	public function call( Template $template = null, $arguments = array() ) {
		if ( is_array( $this->callback ) &&
			isset( $this->callback[0] ) &&
			$this->callback[0] instanceof TemplatesEngineExtensionInterface
		) {
			$this->callback[0]->template = $template;
		}

		return call_user_func_array( $this->callback, $arguments );
	}
}
