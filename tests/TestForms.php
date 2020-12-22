<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Backyard forms test.
 *
 * @package   backyard-foundation
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Tests;

use Backyard\Application;
use Backyard\Forms\Elements\Nonce;
use Backyard\Forms\Filters\SanitizeTextarea;
use Backyard\Forms\Filters\SanitizeTextField;
use Backyard\Forms\Form;
use Backyard\Forms\Renderers\CustomFormRenderer;
use Backyard\Forms\Renderers\TableFormLayout;
use Backyard\Utils\Str;
use Backyard\Plugin;
use Backyard\Templates\TemplatesServiceProvider;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;

class TestForms extends \WP_UnitTestCase {

	/**
	 * Form instance.
	 *
	 * @var Form
	 */
	protected $form;

	/**
	 * Plugin instance
	 *
	 * @var Plugin
	 */
	protected $plugin;

	public function setUp() {
		$this->form = new ExampleForm( 'example_form' );

		$path   = realpath( __DIR__ . '/test-plugin', );
		$plugin = ( Application::get() )->loadPlugin( $path, realpath( __DIR__ . '/test-plugin/test-plugin.php' ), 'config' );

		$plugin->addServiceProvider( TemplatesServiceProvider::class );

		$this->plugin = $plugin;
	}

	public function testFormSetup() {
		$this->assertInstanceOf( Form::class, $this->form );
	}

	public function testFormNonceNameSetup() {

		$this->assertNotEmpty( $this->form->getOption( 'nonce_name' ) );

		$formCustomNonce = new ExampleForm( 'example_form', [ 'nonce_name' => 'my_nonce' ] );

		$this->assertSame( $formCustomNonce->getOption( 'nonce_name' ), 'my_nonce' );

	}

	public function testFormHasNonceField() {
		$this->assertInstanceOf( Nonce::class, $this->form->get( 'example_form' ) );
	}

	public function testFiltersAreAutomaticallyApplied() {

		$filters = $this->form->getInputFilter();

		$exampleField = $filters->get( 'text' );
		$filtersList  = $exampleField->getFilterChain()->getFilters()->toArray();

		$this->assertInstanceOf( SanitizeTextField::class, $filtersList[0][0] );

		$exampleField2 = $filters->get( 'mytextareafield' );
		$filtersList   = $exampleField2->getFilterChain()->getFilters()->toArray();

		$this->assertInstanceOf( SanitizeTextarea::class, $filtersList[0][0] );

	}

	public function testNativeRendering() {

		$this->assertNull( $this->form->getRenderer() );
		$this->assertTrue( Str::startsWith( $this->form->render(), '<form action="" method="POST"' ) );

	}

	public function testTableLayoutRendering() {

		$form = $this->form;
		$form->setCustomRenderer( TableFormLayout::class );

		$this->assertInstanceOf( CustomFormRenderer::class, $form->getCustomRenderer() );
		$this->assertTrue( Str::contains( $form->render(), '<div class="backyard-form table-layout">' ) );

	}

	public function testFormTabsSupport() {

		$form = new ExampleFormWithTabs( 'example_form' );

		$this->assertTrue( $form->hasTabs() );
		$this->assertTrue( count( $form->getTabs() ) === 1 );

		$form->setCustomRenderer( TableFormLayout::class );

		$this->assertTrue( Str::contains( $form->render(), '<nav class="nav-tab-wrapper wp-clearfix">' ) );

	}

	public function testInputSanitizers() {

		$text = new Input( 'text' );
		$text->getFilterChain()->attach( new SanitizeTextField() );

		$textarea = new Input( 'textarea' );
		$textarea->getFilterChain()->attach( new SanitizeTextarea() );

		$inputFilter = new InputFilter();
		$inputFilter->add( $text );
		$inputFilter->add( $textarea );

		$inputFilter->setData( [ 'text' => '<strong>testing</strong>', 'textarea' => '<script>console.log("testing")</script> <div>testing</div> with <strong>bold</strong> text' ] );

		$this->assertEquals( 'testing', $inputFilter->getValue( 'text' ) );
		$this->assertEquals( 'testing with bold text', $inputFilter->getValue( 'textarea' ) );

	}

}

class ExampleForm extends Form {
	public function setupFields() {
		$this->add(
			[
				'type'    => 'text',
				'name'    => 'text',
				'options' => [
					'label' => 'Text field',
					'hint'  => 'Here goes the description',
				],
			]
		);
		$this->add(
			[
				'type'    => 'textarea',
				'name'    => 'mytextareafield',
				'options' => [
					'label' => 'Textarea field',
					'hint'  => 'Here goes the description',
				],
			]
		);
	}
}

class ExampleFormWithTabs extends Form {

	public function setupFields() {
		$this->addTab(
			'test_tab',
			'tab 1',
			[
				[
					'type'    => 'text',
					'name'    => 'text',
					'options' => [
						'label' => 'Text field',
						'hint'  => 'Here goes the description',
					],
				],
			]
		);
	}

}

