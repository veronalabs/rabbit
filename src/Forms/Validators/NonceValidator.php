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

use Laminas\Validator\AbstractValidator;

class NonceValidator extends AbstractValidator {

	const NONCE = 'nonce';

	protected $messageTemplates = [
		self::NONCE => 'Nonce validation failed. Please reload the page and try again.',
	];

	public function isValid( $value ) {
		$this->setValue( $value );

		return true;
	}

}
