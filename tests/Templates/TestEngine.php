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

namespace Backyard\Tests\Templates;

use Backyard\Application;
use Backyard\Plugin;
use Backyard\Templates\Engine;
use Backyard\Templates\Template\Folders;
use Backyard\Templates\Template\Func;
use Backyard\Templates\Template\Template;
use Backyard\Templates\TemplatesServiceProvider;
use Backyard\Utils\Str;
use org\bovigo\vfs\vfsStream;

class TestEngine extends \WP_UnitTestCase {

	protected $plugin;

	protected $engine;

	public function setUp() {

		vfsStream::setup( 'templates' );

		$path   = realpath( BACKYARD_TESTS_PATH . '/test-plugin' );
		$plugin = ( Application::get() )->loadPlugin( $path, realpath( BACKYARD_TESTS_PATH . '/test-plugin/test-plugin.php' ), 'config' );

		$plugin->addServiceProvider( TemplatesServiceProvider::class );
		$plugin->bootPluginProviders();

		$this->plugin = $plugin;
		$this->engine = $this->plugin->get( Engine::class );
	}

	public function testServiceProviderRegistration() {
		$this->assertTrue( $this->plugin->has( Engine::class ) );
	}

	public function testMacroRegistration() {
		$this->assertInstanceOf( Engine::class, $this->plugin->templates() );
	}

	public function testSetFileExtension() {
		$this->assertInstanceOf( Engine::class, $this->engine->setFileExtension( 'tpl' ) );
		$this->assertSame( 'tpl', $this->engine->getFileExtension() );
	}

	public function testSetNullFileExtension() {
		$this->assertInstanceOf( Engine::class, $this->engine->setFileExtension( null ) );
		$this->assertNull( $this->engine->getFileExtension() );
	}

	public function testGetFileExtension() {
		$this->assertSame( 'php', $this->engine->getFileExtension() );
	}

	public function testAddFolder() {
		vfsStream::create(
			array(
				'folder' => array(
					'template.php' => '',
				),
			)
		);

		$this->assertInstanceOf( Engine::class, $this->engine->addFolder( 'folder', vfsStream::url( 'templates/folder' ), 2 ) );
		$this->assertSame( 'vfs://templates/folder', $this->engine->getFolders()->get( 'folder' )->getPath() );
	}

	public function testAddFolderWithNamespaceConflict() {
		// The template folder "name" is already being used.
		$this->expectException( \LogicException::class );
		$this->engine->addFolder( 'name', vfsStream::url( 'templates' ), 2 );
		$this->engine->addFolder( 'name', vfsStream::url( 'templates' ), 2 );
	}

	public function testAddFolderWithInvalidDirectory() {
		// The specified directory path "vfs://does/not/exist" does not exist.
		$this->expectException( \LogicException::class );
		$this->engine->addFolder( 'namespace', vfsStream::url( 'does/not/exist' ), 2 );
	}

	public function testRemoveFolder() {
		vfsStream::create(
			array(
				'folder' => array(
					'template.php' => '',
				),
			)
		);

		$this->engine->addFolder( 'folder', vfsStream::url( 'templates/folder' ), 2 );
		$this->assertTrue( $this->engine->getFolders()->exists( 'folder' ) );
		$this->assertInstanceOf( Engine::class, $this->engine->removeFolder( 'folder' ) );
		$this->assertFalse( $this->engine->getFolders()->exists( 'folder' ) );
	}

	public function testGetFolders() {
		$this->assertInstanceOf( Folders::class, $this->engine->getFolders() );
	}

	public function testAddData() {
		$this->engine->addData( array( 'name' => 'Jonathan' ) );
		$data = $this->engine->getData();
		$this->assertSame( 'Jonathan', $data['name'] );
	}

	public function testAddDataWithTemplate() {
		$this->engine->addData( array( 'name' => 'Jonathan' ), 'template' );
		$data = $this->engine->getData( 'template' );
		$this->assertSame( 'Jonathan', $data['name'] );
	}

	public function testAddDataWithTemplates() {
		$this->engine->addData( array( 'name' => 'Jonathan' ), array( 'template1', 'template2' ) );
		$data = $this->engine->getData( 'template1' );
		$this->assertSame( 'Jonathan', $data['name'] );
	}

	public function testRegisterFunction() {
		vfsStream::create(
			array(
				'template.php' => '<?=$this->uppercase($name)?>',
			)
		);

		$this->engine->registerFunction( 'uppercase', 'strtoupper' );
		$this->assertInstanceOf( Func::class, $this->engine->getFunction( 'uppercase' ) );
		$this->assertSame( 'strtoupper', $this->engine->getFunction( 'uppercase' )->getCallback() );
	}

	public function testDropFunction() {
		$this->engine->registerFunction( 'uppercase', 'strtoupper' );
		$this->assertTrue( $this->engine->doesFunctionExist( 'uppercase' ) );
		$this->engine->dropFunction( 'uppercase' );
		$this->assertFalse( $this->engine->doesFunctionExist( 'uppercase' ) );
	}

	public function testDropInvalidFunction() {
		// The template function "some_function_that_does_not_exist" was not found.
		$this->expectException( \LogicException::class );
		$this->engine->dropFunction( 'some_function_that_does_not_exist' );
	}

	public function testGetFunction() {
		$this->engine->registerFunction( 'uppercase', 'strtoupper' );
		$function = $this->engine->getFunction( 'uppercase' );

		$this->assertInstanceOf( Func::class, $function );
		$this->assertSame( 'uppercase', $function->getName() );
		$this->assertSame( 'strtoupper', $function->getCallback() );
	}

	public function testGetInvalidFunction() {
		// The template function "some_function_that_does_not_exist" was not found.
		$this->expectException( \LogicException::class );
		$this->engine->getFunction( 'some_function_that_does_not_exist' );
	}

	public function testDoesFunctionExist() {
		$this->engine->registerFunction( 'uppercase', 'strtoupper' );
		$this->assertTrue( $this->engine->doesFunctionExist( 'uppercase' ) );
	}

	public function testDoesFunctionNotExist() {
		$this->assertFalse( $this->engine->doesFunctionExist( 'some_function_that_does_not_exist' ) );
	}

	public function testGetTemplatePath() {

		$test = realpath( BACKYARD_TESTS_PATH . '/test-plugin/templates/test.php' );

		$this->assertSame( $test, $this->engine->path( 'test' ) );
	}

	public function testTemplateExists() {
		$this->assertFalse( $this->engine->exists( 'template' ) );

		$this->assertTrue( $this->engine->exists( 'test' ) );
	}

	public function testMakeTemplate() {
		$this->assertInstanceOf( Template::class, $this->engine->make( 'test' ) );
	}

	public function testCanRender() {
		$this->assertTrue( Str::contains( $this->plugin->templates()->render( 'test' ), 'testing' ) );
		$this->assertTrue( Str::contains( $this->plugin->templates()->render( 'test-vars', [ 'name' => 'John' ] ), 'John' ) );
	}
}
