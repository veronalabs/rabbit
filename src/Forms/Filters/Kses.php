<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Backyard forms sanitization filter.
 *
 * @package   backyard-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Forms\Filters;

use Laminas\Filter\FilterInterface;

/**
 * wp_kses_post form filter.
 *
 * @see https://developer.wordpress.org/reference/functions/wp_kses_post/
 */
class Kses implements FilterInterface {

	/**
	 * @inheritDoc
	 */
	public function filter( $value ) {
		return wp_kses_post( $value );
	}

}
