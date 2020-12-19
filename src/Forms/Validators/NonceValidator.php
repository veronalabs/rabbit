<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Backyard forms nonce validator.
 *
 * @package   backyard-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Forms\Validators;

use Backyard\Nonces\NonceFactory;
use Laminas\Validator\AbstractValidator;

/**
 * Verify nonces through forms.
 */
class NonceValidator extends AbstractValidator {

	const NONCE = 'nonce';

	protected $messageTemplates = [
		self::NONCE => 'Nonce validation failed. Please reload the page and try again.',
	];

	/**
	 * Determine if the nonce value submitted is valid.
	 *
	 * We use the "name" option set to the validator to identify the
	 * nonce that needs to be verified.
	 *
	 * @param string $value
	 * @return boolean
	 */
	public function isValid( $value ) {
		$this->setValue( $value );

		if ( ! NonceFactory::check( $this->getOption( 'name' ), $value ) ) {
			$this->error( self::NONCE );
			return false;
		}

		return true;
	}

}
