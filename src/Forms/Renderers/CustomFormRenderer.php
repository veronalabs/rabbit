<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Backyard base rendering form class.
 *
 * @package   backyard-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Forms\Renderers;

use Backyard\Application;
use Backyard\Contracts\FormRendererInterface;
use Backyard\Forms\Form;
use Laminas\Form\Element;
use Laminas\View\Renderer\PhpRenderer;
use League\Plates\Engine;

/**
 * Custom form layout rendering base class.
 */
abstract class CustomFormRenderer {

	/**
	 * Form that will be rendered.
	 *
	 * @var Form
	 */
	protected $form;

	/**
	 * Base rendering engine
	 *
	 * @var PhpRenderer|\Laminas\Form\View\HelperTrait
	 */
	protected $phpRenderer;

	/**
	 * Templates engine.
	 *
	 * @var Engine
	 */
	protected $templatesEngine;

	/**
	 * Initialize the custom layout properties.
	 *
	 * @param Form $form
	 */
	public function __construct( Form $form ) {
		$this->form            = $form;
		$this->phpRenderer     = $this->form->getRenderer();
		$this->templatesEngine = Application::get()->plugin->templates();
	}

	/**
	 * Get the templates engine.
	 *
	 * @return Engine
	 */
	public function getTemplatesEngine() {
		return $this->templatesEngine;
	}

	/**
	 * Render field into a row.
	 *
	 * @param Element $element
	 * @return void
	 */
	public function renderRow( Element $element ) {

	}

	/**
	 * Render field label.
	 *
	 * @param Element $element
	 * @return void
	 */
	public function renderLabel( Element $element ) {

	}

	/**
	 * Render field input.
	 *
	 * @param Element $element
	 * @return void
	 */
	public function renderInput( Element $element ) {

	}

	/**
	 * Render the form through a custom layout.
	 *
	 * @return string
	 */
	public function render() {

		return 'hehehe';

	}

}
