<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Form render interface.
 *
 * @package   backyard-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Contracts;

use Laminas\Form\Element;

interface FormRendererInterface {

	public function renderRow( Element $element );

	public function renderLabel( Element $element );

	public function renderInput( Element $element );

	public function render();

}
