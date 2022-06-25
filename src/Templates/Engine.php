<?php // phpcs:ignore WordPress.Files.FileName

/**
 * Native PHP template system that’s fast, easy to use and easy to extend.
 * Based on plates from phpleague.
 *
 * @package   rabbit-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Rabbit\Templates;

use Rabbit\Application;

/**
 * Templates api and env storage.
 */
class Engine
{
	/**
	 * Directory name where templates are found in this plugin.
	 *
	 * e.g. 'templates' or 'includes/templates', etc.
	 *
	 * @var string
	 */
	protected $pluginTemplatesDirectory;

	/**
	 * Path to the plugin templates directory
	 *
	 * @var string
	 */
	protected $pluginTemplatesPath;

	/**
	 * @var \Rabbit\Plugin
	 */
	private $application;

	/**
	 * @var $template
	 */
	private $template;

	/**
	 * @var array
	 */
	private $data;

	/**
	 * Get things started.
	 *
	 * @param $template
	 * @param $data
	 */
	public function __construct($template, $data = [])
	{
		$this->application = Application::get()->plugin;
		$this->template    = $template;
		$this->data  = $data;

		$this->setPluginTemplatesPath();
	}

	/**
	 * Set up the path to plugin templates directory.
	 *
	 * @param string $directoryName name of the folder.
	 * @return Engine
	 */
	public function setPluginTemplatesPath($directoryName = false)
	{
		if (!$directoryName) {
			$directoryName = $this->application->config('views_path');
		}

		$this->pluginTemplatesDirectory = $directoryName;
		$this->pluginTemplatesPath      = $this->application->basePath($directoryName);

		return $this;
	}

	/**
	 * @return false|string|void
	 */
	public function render()
	{
		$templatePath = sprintf('%s/%s.php', $this->pluginTemplatesPath, $this->template);

		if (file_exists($templatePath)) {
			ob_start();

			extract($this->data);
			require $templatePath;

			return ob_get_clean();
		}
	}

	/**
	 * Get the path to the plugin templates folder.
	 *
	 * @return string
	 */
	public function getPluginTemplatesPath()
	{
		return $this->pluginTemplatesPath;
	}
}
