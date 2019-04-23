<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Asset\DI;

use Chomenko\Asset\AssetProvider;
use Chomenko\Asset\Config;
use Chomenko\Asset\Modifiers;
use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

/**
 * Class AssetExtension
 * @package Chomenko\Asset\DI
 * @property Config $config
 */
class AssetExtension extends CompilerExtension
{

	const TAG_MODIFIER = 'asset.modifier';
	const KEY_PROVIDER = '@asset.provider';

	/**
	 * @var array
	 */
	private $modifiers = [
		"resize" => Modifiers\Resize::class
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$this->config = new Config($this->config);

		foreach ($this->modifiers as $alias => $class) {
			$builder->addDefinition($this->prefix("modifier.$alias"))
				->setFactory($class)
				->setTags([self::TAG_MODIFIER => $alias]);
		}

		$builder->addDefinition($this->prefix("provider"))
			->setFactory(AssetProvider::class, [
				"config" => $this->config
			]);
	}

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$engine = $builder->getDefinition('nette.latteFactory');
		$provider = $builder->getDefinition($this->prefix("provider"));

		$engine->addSetup('Chomenko\Asset\Macros\Latte::install(?, ?)', [
			"@self", $builder->getDefinition($this->prefix("provider"))
		]);

		foreach ($builder->findByTag(self::TAG_MODIFIER) as $serviceName => $alias) {
			$provider->addSetup('$service->addModifier(?, ?)', [
				$alias, $serviceName
			]);
		}
	}

	/**
	 * @param Configurator $configurator
	 */
	public static function register(Configurator $configurator)
	{
		$configurator->onCompile[] = function ($config, Compiler $compiler) {
			$compiler->addExtension('asset', new AssetExtension());
		};
	}

}
