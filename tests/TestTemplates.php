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

namespace Backyard\Tests;

use Backyard\Plugin;
use Backyard\Templates\TemplatesServiceProvider;
use Backyard\Utils\Str;
use League\Plates\Engine;

class TestTemplates extends \WP_UnitTestCase {

	protected $plugin;

	public function setUp() {
		$path   = realpath( __DIR__ . '/test-plugin' );
		$plugin = new Plugin( $path, realpath( __DIR__ . '/test-plugin/test-plugin.php' ), 'config' );

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

	public function testCanRender() {
		$this->assertTrue( Str::contains( $this->plugin->templates()->render( 'test' ), 'testing' ) );
		$this->assertTrue( Str::contains( $this->plugin->templates()->render( 'test-vars', [ 'name' => 'John' ] ), 'John' ) );
	}

}
