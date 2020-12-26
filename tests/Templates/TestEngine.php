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
use Backyard\Templates\TemplatesServiceProvider;
use org\bovigo\vfs\vfsStream;

class TestEngine extends \WP_UnitTestCase {

	protected $plugin;

	public function setUp() {

		$path   = realpath( BACKYARD_TESTS_PATH . '/test-plugin' );
		$plugin = ( Application::get() )->loadPlugin( $path, realpath( BACKYARD_TESTS_PATH . '/test-plugin/test-plugin.php' ), 'config' );

		$plugin->addServiceProvider( TemplatesServiceProvider::class );
		$plugin->bootPluginProviders();

		$this->plugin = $plugin;
	}

	public function testServiceProviderRegistration() {
		$this->assertTrue( $this->plugin->has( Engine::class ) );
	}

	public function testMacroRegistration() {
		$this->assertInstanceOf( Engine::class, $this->plugin->templates() );
	}

}
