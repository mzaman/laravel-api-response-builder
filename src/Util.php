<?php
declare(strict_types=1);

namespace MarcinOrlowski\ResponseBuilder;

/**
 * Laravel API Response Builder
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @copyright 2016-2025 Marcin Orlowski
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/laravel-api-response-builder
 */

use MarcinOrlowski\ResponseBuilder\Exceptions as Ex;

/**
 * Commonly used utility functions
 */
final class Util
{
	/**
	 * Merges the configs together and takes multi-dimensional arrays into account.
	 * Support for multi-dimensional config array. Built-in config merge only supports flat arrays.
	 * Throws \RuntimeException if arrays stucture causes type conflics (i.e. you want to merge
	 * array with int).
	 *
	 * @param array<string, mixed> $original Array to merge other array into. Usually default values to overwrite.
	 * @param array<string, mixed> $merging  Array with items to be merged into $original, overriding (primitives) or merging
	 *                        (arrays) entries in destination array.
	 *
	 * @return array<string, mixed>
	 *
	 * @throws Ex\IncompatibleTypeException
	 */
	public static function mergeConfig(array $original, array $merging): array
	{
		$array = $original;
		foreach ($merging as $m_key => $m_val) {
			if (\array_key_exists($m_key, $original)) {
				$orig_type = \gettype($original[ $m_key ]);
				$m_type = \gettype($m_val);
				if ($orig_type !== $m_type) {
					throw new Ex\IncompatibleTypeException(
						"mergeConfig(): Cannot merge '{$m_type}' into '{$orig_type}' for key '{$m_key}'.");
				}

				if (\is_array($m_val)) {
					/** @noinspection PhpUnnecessaryStaticReferenceInspection */
					/** @var array<string, mixed> $original_value */
					$original_value = $original[ $m_key ];
					/** @var array<string, mixed> $m_val */
					$array[ $m_key ] = static::mergeConfig($original_value, $m_val);
				} else {
					$array[ $m_key ] = $m_val;
				}
			} else {
				$array[ $m_key ] = $m_val;
			}
		}

		return $array;
	}

	/**
	 * Sorts array (in place) by value, assuming value is an array and contains `pri` key with integer
	 * (positive/negative) value which is used for sorting higher -> lower priority.
	 *
	 * @param array<string, mixed> $array
	 */
	public static function sortArrayByPri(array &$array): void
	{
		uasort($array, static function($array_a, $array_b) {
			/** @var array<string, mixed> $array_a */
			/** @var array<string, mixed> $array_b */
			$pri_a = $array_a['pri'] ?? 0;
			$pri_b = $array_b['pri'] ?? 0;

			return $pri_b <=> $pri_a;
		});
	}

	/**
	 * Checks if given array uses custom (non numeric) keys.
	 *
	 * @param array<string, mixed> $data
	 */
	public static function isArrayWithNonNumericKeys(array $data): bool
	{
		foreach (\array_keys($data) as $key) {
			if (!\is_int($key)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Merges API code mappings, with user codes overriding base codes.
	 *
	 * @param array<int, string> $original Base API code to message key mappings
	 * @param array<int, string> $merging User API code to message key mappings
	 *
	 * @return array<int, string> Merged API code mappings
	 */
	public static function mergeApiCodeMap(array $original, array $merging): array
	{
		return array_replace($original, $merging);
	}

} // end of class
