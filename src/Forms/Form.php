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
	 * Renderer object.
	 *
	 * @var PhpRenderer|FormRendererInterface|null
	 */
	protected $renderer;

	/**
	 * Set a custom form layout renderer.
	 *
	 * @param FormRendererInterface $renderer
	 * @return void
	 */
	public function setRenderer( FormRendererInterface $renderer ) {
		$this->renderer = $renderer;
	}

	/**
	 * Get the rendering handler.
	 *
	 * If no custom layout is set, we use the default php rendering engine.
	 *
	 * @return PhpRenderer|FormRendererInterface|null
	 */
	public function getRenderer() {

		if ( $this->renderer instanceof FormRendererInterface ) {
			return $this->renderer;
		} else {
			$this->renderer = $this->getPhpRenderer();
		}

		return $this->renderer;
	}

	/**
	 * Return an instance of the laminas php rendering engine.
	 *
	 * @return PhpRenderer
	 */
	private function getPhpRenderer() {
		$renderer = new PhpRenderer();
		$renderer->getHelperPluginManager()->configure(
			( new ConfigProvider() )->getViewHelperConfig()
		);

		return $renderer;
	}

}
