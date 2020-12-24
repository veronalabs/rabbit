<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Templates file name.
 *
 * @package   backyard-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Templates\Template;

use LogicException;
use Backyard\Templates\Engine;

/**
 * Single template file name.
 */
class Name {

	/**
	 * Instance of the template engine.
	 *
	 * @var Engine
	 */
	protected $engine;

	/**
	 * The original name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The parsed template folder.
	 *
	 * @var Folder
	 */
	protected $folder;

	/**
	 * The parsed template filename.
	 *
	 * @var string
	 */
	protected $file;

	/**
	 * Create a new Name instance.
	 *
	 * @param Engine $engine
	 * @param string $name
	 */
	public function __construct( Engine $engine, $name ) {
		$this->setEngine( $engine );
		$this->setName( $name );
	}

	/**
	 * Set the engine.
	 *
	 * @param  Engine $engine
	 * @return Name
	 */
	public function setEngine( Engine $engine ) {
		$this->engine = $engine;

		return $this;
	}

	/**
	 * Get the engine.
	 *
	 * @return Engine
	 */
	public function getEngine() {
		return $this->engine;
	}

	/**
	 * Set the original name and parse it.
	 *
	 * @param  string $name
	 * @throws LogicException When template file name is not valid.
	 * @return Name
	 */
	public function setName( $name ) {

		$this->name = $name;
		$this->setFile( $name );

		return $this;
	}

	/**
	 * Get the original name.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set the parsed template folder.
	 *
	 * @param  string $folder
	 * @return Name
	 */
	public function setFolder( $folder ) {
		$this->folder = $this->engine->getFolders()->get( $folder );

		return $this;
	}

	/**
	 * Get the parsed template folder.
	 *
	 * @return string
	 */
	public function getFolder() {
		return $this->folder;
	}

	/**
	 * Set the parsed template file.
	 *
	 * @param  string $file
	 * @throws LogicException When template file name is not valid.
	 * @return Name
	 */
	public function setFile( $file ) {
		if ( $file === '' ) {
			throw new LogicException(
				'The template name "' . $this->name . '" is not valid. ' .
				'The template name cannot be empty.'
			);
		}

		$this->file = $file;

		if ( ! is_null( $this->engine->getFileExtension() ) ) {
			$this->file .= '.' . $this->engine->getFileExtension();
		}

		return $this;
	}

	/**
	 * Get the parsed template file.
	 *
	 * @return string
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 * Resolve template path.
	 *
	 * @return string
	 */
	public function getPath() {

		$folders = $this->engine->getFolders();

		foreach ( $folders as $templatePath ) {
			if ( file_exists( trailingslashit( $templatePath ) . $this->file ) ) {
				return trailingslashit( $templatePath ) . $this->file;
			}
		}

		return trailingslashit( $this->getDefaultDirectory() ) . $this->file;
	}

	/**
	 * Check if template path exists.
	 *
	 * @return boolean
	 */
	public function doesPathExist() {
		return is_file( $this->getPath() );
	}

	/**
	 * Get the default templates directory.
	 *
	 * @throws LogicException When template file name is not valid.
	 * @return string
	 */
	protected function getDefaultDirectory() {
		$directory = $this->engine->getPluginTemplatesPath();

		if ( is_null( $directory ) ) {
			throw new LogicException(
				'The template name "' . $this->name . '" is not valid. ' .
				'The default directory has not been defined.'
			);
		}

		return $directory;
	}
}
