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

use Backyard\Application;
use Backyard\Templates\Engine;
use Backyard\Templates\Template\Folder;
use Backyard\Templates\Template\Name;
use Backyard\Templates\TemplatesServiceProvider;
use org\bovigo\vfs\vfsStream;

class TestName extends \WP_UnitTestCase {

	protected $plugin;

	protected $engine;

	public function setUp() {

		vfsStream::setup( 'templates' );

		vfsStream::create(
			array(
				'template.php' => '',
				'fallback.php' => '',
				'folder'       => array(
					'template.php' => '',
				),
			)
		);

		$path   = realpath( BACKYARD_TESTS_PATH . '/test-plugin' );
		$plugin = ( Application::get() )->loadPlugin( $path, realpath( BACKYARD_TESTS_PATH . '/test-plugin/test-plugin.php' ), 'config' );

		$plugin->addServiceProvider( TemplatesServiceProvider::class );
		$plugin->bootPluginProviders();

		$this->plugin = $plugin;
		$this->engine = $this->plugin->get( Engine::class );
		$this->engine->addFolder( 'folder', vfsStream::url( 'templates/folder' ) );
	}


	public function testCanCreateInstance() {
		$this->assertInstanceOf( Name::class, new Name( $this->engine, 'template' ) );
	}

	public function testGetEngine() {
		$name = new Name( $this->engine, 'template' );

		$this->assertInstanceOf( Engine::class, $name->getEngine() );
	}

	public function testGetName() {
		$name = new Name( $this->engine, 'template' );

		$this->assertSame( 'template', $name->getName() );
	}

	public function testGetFolder() {
		$name   = new Name( $this->engine, 'folder::template' );
		$folder = $name->getFolder();

		$this->assertInstanceOf( Folder::class, $folder );
		$this->assertSame( 'folder', $name->getFolder()->getName() );
	}

	public function testGetFile() {
		$name = new Name( $this->engine, 'template' );

		$this->assertSame( 'template.php', $name->getFile() );
	}

	public function testGetPath() {
		$name = new Name( $this->engine, 'template' );

		$this->assertSame( 'vfs://templates/folder/template.php', $name->getPath() );
	}

	public function testGetPathWithFolder() {
		$name = new Name( $this->engine, 'folder::template' );

		$this->assertSame( 'vfs://templates/folder/template.php', $name->getPath() );
	}

	public function testTemplateExists() {
		$name = new Name( $this->engine, 'template' );

		$this->assertTrue( $name->doesPathExist() );
	}

	public function testTemplateDoesNotExist() {
		$name = new Name( $this->engine, 'missing' );

		$this->assertFalse( $name->doesPathExist() );
	}

	public function testParse() {
		$name = new Name( $this->engine, 'template' );

		$this->assertSame( 'template', $name->getName() );
		$this->assertNull( $name->getFolder() );
		$this->assertSame( 'template.php', $name->getFile() );
	}

	public function testParseWithEmptyTemplateName() {
		// The template name cannot be empty.
		$this->expectException( \LogicException::class );

		$name = new Name( $this->engine, '' );
	}

	public function testParseWithFolder() {
		$name = new Name( $this->engine, 'folder::template' );

		$this->assertSame( 'folder::template', $name->getName() );
		$this->assertSame( 'folder', $name->getFolder()->getName() );
		$this->assertSame( 'template.php', $name->getFile() );
	}

	public function testParseWithFolderAndEmptyTemplateName() {
		// The template name cannot be empty.
		$this->expectException( \LogicException::class );

		$name = new Name( $this->engine, 'folder::' );
	}

	public function testParseWithInvalidName() {
		// Do not use the folder namespace separator "::" more than once.
		$this->expectException( \LogicException::class );

		$name = new Name( $this->engine, 'folder::template::wrong' );
	}

	public function testParseWithNoFileExtension() {
		$this->engine->setFileExtension( null );

		$name = new Name( $this->engine, 'template.php' );

		$this->assertSame( 'template.php', $name->getName() );
		$this->assertNull( $name->getFolder() );
		$this->assertSame( 'template.php', $name->getFile() );
	}
}
