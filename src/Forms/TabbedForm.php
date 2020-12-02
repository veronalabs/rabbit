<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Add tabs support to laminas forms.
 *
 * @package   backyard-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Forms;

trait TabbedForm {

	/**
	 * List of tabs defined for the form.
	 *
	 * @var array
	 */
	protected $tabs = [];

	/**
	 * Determine if the form has tabs.
	 *
	 * @return boolean
	 */
	public function hasTabs() {
		return ! empty( $this->tabs );
	}

	/**
	 * Get the list of tabs for the form.
	 *
	 * @return array
	 */
	public function getTabs() {
		return $this->tabs;
	}

	/**
	 * Add a single tab to the form.
	 *
	 * @param string $id
	 * @param string $label
	 * @return void
	 */
	public function addTab( string $id, string $label ) {
		$this->tabs[ $id ] = $label;
	}

	/**
	 * Add tabs to the form.
	 *
	 * @param array $tabs
	 * @return void
	 */
	public function addTabs( array $tabs ) {
		$this->tabs = $tabs;
	}

}
