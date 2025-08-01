<?php
declare(strict_types=1);

namespace MarcinOrlowski\ResponseBuilder\Contracts;

/**
 * Laravel API Response Builder
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @copyright 2016-2025 Marcin Orlowski
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/laravel-api-response-builder
 */
interface InvalidTypeExceptionContract extends \Throwable
{
	/**
	 * @param array<string> $allowed_types
	 */
	public function __construct(string $var_name, string $type, array $allowed_types);
}
