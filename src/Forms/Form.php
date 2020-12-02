<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Backyard base form class.
 *
 * @package   backyard-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Forms;

use Laminas\Form\Form as LaminasForm;
use Laminas\View\Renderer\PhpRenderer;
use Backyard\Contracts\FormRendererInterface;
use Backyard\Forms\Renderers\BaseFormRenderer;
use Backyard\Forms\Renderers\CustomFormRenderer;
use Laminas\Form\ConfigProvider;

/**
 * Backyard forms builder.
 */
abstract class Form extends LaminasForm {

	/**
	 * Laminas php rendering engine.
	 *
	 * @var PhpRenderer|\Laminas\Form\View\HelperTrait|null
	 */
	protected $renderer;

	/**
	 * Custom Backyard framework form rendering layout.
	 *
	 * @var FormRendererInterface|null
	 */
	protected $customRenderer;

	/**
	 * Set a custom form layout renderer.
	 *
	 * @param string $renderer class path to custom render.
	 * @return void
	 */
	public function setCustomRenderer( string $renderer ) {
		if ( ! $this->renderer ) {
			$this->makeRenderer();
		}
		$this->customRenderer = new $renderer( $this );
	}

	/**
	 * Get the Laminas php rendering engine.
	 *
	 * @return PhpRenderer|null
	 */
	public function getRenderer() {
		return $this->renderer;
	}

	/**
	 * Get the custom rendering object.
	 *
	 * @return FormRendererInterface|null
	 */
	public function getCustomRenderer() {
		return $this->customRenderer;
	}

	/**
	 * Setup the Laminas rendering engine.
	 *
	 * @return void
	 */
	public function makeRenderer() {
		/** @var PhpRenderer|Laminas\Form\View\HelperTrait $renderer */
		$renderer = new PhpRenderer();
		$renderer->getHelperPluginManager()->configure(
			( new ConfigProvider() )->getViewHelperConfig()
		);
		$this->renderer = $renderer;
	}

	/**
	 * Render the form.
	 *
	 * @return string
	 */
	public function render() {

		$output = false;

		if ( ! $this->customRenderer ) {
			$this->makeRenderer();
			$output = $this->renderer->form( $this );
		} elseif ( $this->customRenderer instanceof CustomFormRenderer ) {
			$output = $this->customRenderer->render();
		}

		return $output;

	}

}
