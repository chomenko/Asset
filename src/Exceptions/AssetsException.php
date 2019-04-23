<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */
namespace Chomenko\Asset\Exceptions;

use Chomenko\Asset\AssetFile;

class AssetsException extends \Exception
{

	/**
	 * @param string $name
	 * @return AssetsException
	 */
	public static function notInstalledModifier(string $name): AssetsException
	{
		return new self("Modifier '{$name}' is not installed.");
	}

	/**
	 * @param string $name
	 * @return AssetsException
	 */
	public static function fileDoesNotExist(string $name): AssetsException
	{
		return new self("File '{$name}' does not exist.");
	}

	/**
	 * @param mixed $file
	 * @return AssetsException
	 */
	public static function fileMustInstance($file): AssetsException
	{
		$class = AssetFile::class;
		$type = gettype($file);
		if (is_object($file)) {
			$type = get_class($file);
		}
		return new self("File must by type 'string' or '{$class}' or 'SplFileInfo' you type '$type'");
	}

}