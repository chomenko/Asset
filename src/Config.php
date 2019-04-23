<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Asset;

class Config
{

	/**
	 * @var string
	 */
	private $wwwDir;

	/**
	 * @var string
	 */
	private $assetDir;

	/**
	 * Config constructor.
	 * @param array $configs
	 */
	public function __construct(array $configs)
	{
		foreach (get_class_vars(Config::class) as $name => $default) {
			if (!array_key_exists($name, $configs)) {
				continue;
			}
			$method = "set" . lcfirst($name);
			if (method_exists($this, $method)) {
				$this->{$method}($configs[$name]);
				continue;
			}
			$this->{$name} = $configs[$name];
		}
	}

	/**
	 * @return string
	 */
	public function getWwwDir(): string
	{
		return $this->wwwDir;
	}

	/**
	 * @param string $wwwDir
	 */
	public function setWwwDir(string $wwwDir): void
	{
		$this->wwwDir = $wwwDir;
	}

	/**
	 * @return string
	 */
	public function getAssetDir(): string
	{
		return $this->assetDir;
	}

	/**
	 * @param string $assetDir
	 */
	public function setAssetDir(string $assetDir): void
	{
		if (!file_exists($assetDir)) {
			mkdir($assetDir, 0777, TRUE);
		}
		$this->assetDir = $assetDir;
	}

}