<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Custom rendering handler for the nonce field.
 *
 * @package   backyard-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Forms\Renderers;

use Backyard\Nonces\NonceFactory;
use Laminas\Form\ElementInterface;
use Laminas\Form\View\Helper\FormHidden;

/**
 * Handles the rendering of a nonce field for the form.
 *
 * This is needed because nonce fields in WP uses 2 hidden inputs.
 */
class NonceFieldRenderer extends FormHidden {

	/**
	 * Render nonce field via wp's internal functions.
	 *
	 * @param ElementInterface $element
	 * @return string
	 */
	public function render( ElementInterface $element ) {
		return NonceFactory::fields( $element->getName() );
	}

}
