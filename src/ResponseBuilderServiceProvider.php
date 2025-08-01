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

/**
 * Disable return type hint inspection as we do not have it specified in that
 * class for a purpose. The base class is also not having return type hints.
 *
 * @noinspection ReturnTypeCanBeDeclaredInspection
 */

use Illuminate\Support\ServiceProvider;
use MarcinOrlowski\ResponseBuilder\Exceptions as Ex;

/**
 * Laravel's service provider for ResponseBuilder
 */
class ResponseBuilderServiceProvider extends ServiceProvider
{
	/** @var string[] */
	protected $config_files = [
		'response_builder.php',
	];

	/**
	 * Register bindings in the container.
	 *
	 * @return void
	 *
	 * @throws Ex\IncompatibleTypeException
	 * @throws Ex\IncompleteConfigurationException
	 *
	 * @noinspection PhpUnused
	 * @noinspection ReturnTypeCanBeDeclaredInspection
	 * @noinspection UnknownInspectionInspection
	 */
	public function register()
	{
		foreach ($this->config_files as $file) {
			/** @noinspection PhpUnhandledExceptionInspection */
			$this->mergeConfigFrom(__DIR__ . "/../config/{$file}", ResponseBuilder::CONF_CONFIG);
		}
	}

	/**
	 * Sets up package resources
	 *
	 * @return void
	 *
	 * @noinspection PhpUnused
	 * @noinspection ReturnTypeCanBeDeclaredInspection
	 * @noinspection UnknownInspectionInspection
	 */
	public function boot()
	{
		$this->loadTranslationsFrom(__DIR__ . '/lang', 'response-builder');

		foreach ($this->config_files as $file) {
			$this->publishes([__DIR__ . "/../config/{$file}" => config_path($file)]);
		}
	}

	/**
	 * Merge the given configuration with the existing configuration.
	 *
	 * @param string $path
	 * @param string $key
	 *
	 * @return void
	 *
	 * @throws Ex\IncompleteConfigurationException
	 * @throws Ex\IncompatibleTypeException
	 *
	 * NOTE: not typehints due to compatibility with Laravel's method signature.
	 * @noinspection PhpMissingReturnTypeInspection
	 * @noinspection ReturnTypeCanBeDeclaredInspection
	 * @noinspection PhpMissingParamTypeInspection
	 */
	protected function mergeConfigFrom($path, $key)
	{
		/** @noinspection PhpIncludeInspection */
		/** @noinspection UsingInclusionReturnValueInspection */
		$defaults = require $path;
        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;
		$config = $app['config']->get($key, []);

		/** @noinspection PhpUnhandledExceptionInspection */
		/** @var array<string, mixed> $config */
		/** @var array<string, mixed> $defaults */
		$merged_config = Util::mergeConfig($defaults, $config);

		/** @var array<string, mixed> $merged_config */
		/** @phpstan-ignore-next-line offsetAccess.nonOffsetAccessible */
		if (!isset($merged_config['converter']['classes'])) {
			throw new Ex\IncompleteConfigurationException(
				sprintf('Configuration lacks "%s" array.', ResponseBuilder::CONF_KEY_CONVERTER_CLASSES));
		}
		/** @var array<string, mixed> $converter_config */
		$converter_config = $merged_config['converter'];

		/** @var array<string, mixed> $converter_classes */
		$converter_classes = $converter_config['classes'];
		Util::sortArrayByPri($converter_classes);
		$merged_config['converter']['classes'] = $converter_classes;

        $app['config']->set($key, $merged_config);
    }

} // end of class
