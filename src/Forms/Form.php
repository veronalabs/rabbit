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
use Laminas\Form\ConfigProvider;

/**
 * Backyard forms builder.
 */
abstract class Form extends LaminasForm {

	/**
	 * Laminas php rendering engine.
	 *
	 * @var PhpRenderer|null
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
	 * @param FormRendererInterface $renderer
	 * @return void
	 */
	public function setCustomRenderer( FormRendererInterface $renderer ) {
		$this->customRenderer = $renderer;
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

		$this->makeRenderer();

		$output = false;

		if ( ! $this->customRenderer ) {
			$output = $this->renderer->form( $this );
		}

		return $output;

	}

}
