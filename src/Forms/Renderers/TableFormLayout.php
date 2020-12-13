<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Uses tables to render forms. This is usually used within the admin panel.
 *
 * @package   backyard-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Forms\Renderers;

use Backyard\Forms\Form;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Tel;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Url;

/**
 * Tabled forms layout for admin pages.
 */
class TableFormLayout extends CustomFormRenderer {

	/**
	 * Setup the form and automatically add classes to form fields.
	 *
	 * @param Form $form
	 */
	public function __construct( Form $form ) {
		parent::__construct( $form );
		$this->setupClasses();
	}

	/**
	 * Automatically add classes to some field types.
	 *
	 * @return void
	 */
	private function setupClasses() {

		foreach ( $this->form as $field ) {

			$classes = $field->getAttribute( 'class' );

			$classes .= ' bk-input';

			if ( $field instanceof Submit ) {
				$classes .= ' button button-primary';
			}

			if (
				$field instanceof Text ||
				$field instanceof Email ||
				$field instanceof Password ||
				$field instanceof Tel ||
				$field instanceof Url
			) {
				$classes .= ' regular-text';
			}

			$field->setAttribute( 'class', trim( $classes ) );

		}

	}

	/**
	 * Render a tabled form.
	 *
	 * @return string
	 */
	public function render() {

		$templates = $this->getTemplatesEngine();

		return $templates->render( 'forms/table-layout', [ 'form' => $this->form ] );

	}

}
