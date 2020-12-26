<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Backyard plugin templates testing
 *
 * @package   backyard-foundation
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Tests\Templates\Template;

use Backyard\Templates\Template\Data;

class TestData extends \WP_UnitTestCase {
	private $template_data;

	public function setUp(): void {
		$this->template_data = new Data();
	}

	public function testCanCreateInstance() {
		$this->assertInstanceOf( Data::class, $this->template_data );
	}

	public function testAddDataToAllTemplates() {
		$this->template_data->add( array( 'name' => 'John' ) );
		$data = $this->template_data->get();
		$this->assertSame( 'John', $data['name'] );
	}

	public function testAddDataToOneTemplate() {
		$this->template_data->add( array( 'name' => 'John' ), 'template' );
		$data = $this->template_data->get( 'template' );
		$this->assertSame( 'John', $data['name'] );
	}

	public function testAddDataToOneTemplateAgain() {
		$this->template_data->add( array( 'firstname' => 'John' ), 'template' );
		$this->template_data->add( array( 'lastname' => 'Doe' ), 'template' );
		$data = $this->template_data->get( 'template' );
		$this->assertSame( 'Doe', $data['lastname'] );
	}

	public function testAddDataToSomeTemplates() {
		$this->template_data->add( array( 'name' => 'John' ), array( 'template1', 'template2' ) );
		$data = $this->template_data->get( 'template1' );
		$this->assertSame( 'John', $data['name'] );
	}

	public function testAddDataWithInvalidTemplateFileType() {
		// The templates variable must be null, an array or a string, integer given.
		$this->expectException( \LogicException::class );
		$this->template_data->add( array( 'name' => 'John' ), 123 );
	}
}
