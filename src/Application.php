<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Rabbit plugin application.
 *
 * @package   rabbit-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Rabbit;

use Rabbit\Utils\Singleton;

/**
 * Framework application wrapper.
 */
class Application extends Singleton {

	/**
	 * Plugin instance
	 *
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Load a plugin into the application.
	 *
	 * @param string $basePath path of the plugin
	 * @param string $filePath path of the plugin entry file
	 * @param string $configFolder relative path to the configuration folder.
	 * @return Plugin
	 */
	public function loadPlugin( $basePath, $filePath, $configFolder = null ) {
		$this->plugin = new Plugin( $basePath, $filePath, $configFolder );

		return $this->plugin;
	}
}
