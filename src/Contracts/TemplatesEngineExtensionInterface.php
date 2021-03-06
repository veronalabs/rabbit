<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Definition of the public contract to be available on a templates engine extension instance.
 *
 * @package   rabbit-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Rabbit\Contracts;

use Rabbit\Templates\Engine;

interface TemplatesEngineExtensionInterface {

	/**
	 * Register templates engine extension.
	 *
	 * @param Engine $engine
	 * @return void
	 */
	public function register( Engine $engine );
}
