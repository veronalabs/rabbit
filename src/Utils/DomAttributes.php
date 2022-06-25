<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Attributes generator for html tags.
 *
 * @package   rabbit-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Rabbit\Utils;

use Rabbit\Application;

/**
 * Generate dom attributes for html tags.
 */
class DomAttributes {

	const ID          = 'id';
	const CLASS_NAME  = 'class';
	const STYLE       = 'style';
	const TITLE       = 'title';
	const TYPE        = 'type';
	const VALUE       = 'value';
	const PLACEHOLDER = 'placeholder';

	/**
	 * Attributes list.
	 *
	 * @var array
	 */
	private $attributes = array();

	/**
	 * Add attributes.
	 *
	 * @param string $context context
	 * @param array  $attr attributes to add
	 * @return DomAttributes
	 */
	public function add( string $context, array $attr ): DomAttributes {
		$this->attributes[ $context ] = $attr;
		return $this;
	}

	/**
	 * Get attributes
	 *
	 * @param string $context context to retrieve
	 * @return array<string|bool>
	 */
	public function get( string $context ): array {
		return $this->attributes[ $context ];
	}

	/**
	 * Determine if attributes exist for the given context.
	 *
	 * @param string $context context
	 * @return bool
	 */
	public function has( string $context ): bool {
		return \array_key_exists( $context, $this->attributes );
	}

	/**
	 * Remove attributes from a given context.
	 *
	 * @param string $context context
	 * @return DomAttributes
	 */
	public function remove( string $context ) {
		unset( $this->attributes[ $context ] );
		return $this;
	}

	/**
	 * Build list of attributes into a string and apply contextual filter on string.
	 *
	 * The contextual filter is of the form `pressmodo_attr_{context}_output`.
	 *
	 * @param  string $context The context, to build filter name.
	 * @param  mixed  $args    Optional. Extra arguments in case is needed.
	 * @return string          String of HTML attributes and values.
	 */
	public function render( string $context, $args = null ): string {

		$prefix = strtolower( Application::get()->plugin->getHeader( 'plugin_prefix' ) );

		/**
		 * This filters the array with html attributes.
		 *
		 * @param  array  $attr    The array with all HTML attributes to render.
		 * @param  string $context The context in wich this functionis called.
		 * @param  null   $args    Optional. Extra arguments in case is needed.
		 *
		 * @var array $attr
		 */
		$attr = (array) \apply_filters( "{$prefix}_{$context}_attr", $this->get( $context ), $context, $args );

		/**
		 * This filters the output of the html attributes.
		 *
		 * @param  string $html    The HTML attr output.
		 * @param  array  $attr    The array with all HTML attributes to render.
		 * @param  string $context The context in wich this functionis called.
		 * @param  null   $args    Optional. Extra arguments in case is needed.
		 *
		 * @var string
		 */
		$html = \apply_filters(
			"{$prefix}_attr_{$context}_output",
			$this->generateValidHtml( $attr ),
			$attr,
			$context,
			$args
		);

		$this->remove( $context );
		return \strval( $html );
	}

	/**
	 * Generate html.
	 *
	 * @param array $attr attributes
	 * @return string
	 */
	private function generateValidHtml( array $attr ): string {

		/**
		 * @link https://www.php.net/manual/en/function.array-reduce.php#118254
		 */
		return \array_reduce(
			\array_keys( $attr ),
			function ( $html, $key ) use ( $attr ) {

				if ( empty( $attr[ $key ] ) ) {
					return $html;
				}

				if ( true === $attr[ $key ] ) {
					return $html . ' ' . \esc_html( $key );
				}

				return $html . \sprintf(
					' %s="%s"',
					esc_html( $key ),
					( 'href' === $key ) ? \esc_url( $attr[ $key ] ) : \esc_attr( $attr[ $key ] )
				);
			},
			''
		);
	}

}
