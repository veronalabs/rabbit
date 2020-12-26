<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Templates engine collection of folders.
 *
 * @package   backyard-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Templates\Template;

use LogicException;

/**
 * Templates engine folders collection handler.
 */
class Folders {

	/**
	 * Array of template folders.
	 *
	 * @var array
	 */
	protected $folders = array();

	/**
	 * Add a template folder.
	 *
	 * @param  string  $name
	 * @param  string  $path
	 * @param  boolean $priority
	 * @throws LogicException When folder already exists.
	 * @return Folders
	 */
	public function add( $name, $path, $priority = 20 ) {
		if ( $this->exists( $name ) ) {
			throw new LogicException( 'The template folder "' . $name . '" is already being used.' );
		}

		$this->folders[ $name ] = new Folder( $name, $path, $priority );

		return $this;
	}

	/**
	 * Remove a template folder.
	 *
	 * @param  string $name
	 * @throws LogicException When folder is not found.
	 * @return Folders
	 */
	public function remove( $name ) {
		if ( ! $this->exists( $name ) ) {
			throw new LogicException( 'The template folder "' . $name . '" was not found.' );
		}

		unset( $this->folders[ $name ] );

		return $this;
	}

	/**
	 * Get a template folder.
	 *
	 * @param  string $name
	 * @throws LogicException When folder is not found.
	 * @return Folder
	 */
	public function get( $name ) {
		if ( ! $this->exists( $name ) ) {
			throw new LogicException( 'The template folder "' . $name . '" was not found.' );
		}

		return $this->folders[ $name ];
	}

	/**
	 * Check if a template folder exists.
	 *
	 * @param  string $name
	 * @return boolean
	 */
	public function exists( $name ) {
		return isset( $this->folders[ $name ] );
	}

	/**
	 * Get all registered folders ordered by priority.
	 *
	 * @return array
	 */
	public function getOrdered() {

		$folders = [];

		/** @var Folder $folder */
		foreach ( $this->folders as $folderName => $folder ) {
			$folders[ $folder->getPriority() ] = $folder->getPath();
		}

		ksort( $folders, SORT_NUMERIC );

		return array_map( 'trailingslashit', $folders );

	}

}
