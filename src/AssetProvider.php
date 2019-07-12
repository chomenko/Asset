<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Asset;

use Chomenko\Asset\Exceptions\AssetsException;
use Chomenko\Asset\Modifiers\IModifier;
use Nette\DI\Container;
use Nette\Http\IRequest;
use Nette\Http\Url;

class AssetProvider
{

	/**
	 * @var Config
	 */
	private $config;

	/**
	 * @var array
	 */
	private $modifiers = [];

	/**
	 * @var IRequest
	 */
	private $request;
	/**
	 * @var Container
	 */
	private $container;

	/**
	 * @param Config $config
	 * @param Url $request
	 */
	public function __construct(Config $config, IRequest $request, Container $container)
	{
		$this->config = $config;
		$this->request = $request;
		$this->container = $container;
	}

	/**
	 * @param string|AssetFile|\SplFileInfo $file
	 * @param string|null $modifierName
	 * @param array ...$args
	 * @return Url|string
	 * @throws AssetsException
	 */
	public function link($file, ?string $modifierName = NULL, ...$args)
	{
		$fileInfo = new File($file);
		if ($modifierName) {
			$modifier = $this->getModifier($modifierName);
			if (!$modifier) {
				throw AssetsException::notInstalledModifier($modifierName);
			}
			$modifier->modify($this, $fileInfo, $args);
		}

		if (!file_exists($fileInfo->getFileInfo()->getPathname())) {
			return "#file not exists";
		}

		$linkedFile = $fileInfo->getFileLink();
		$assetAbsolute = $this->getAbsolutePath($this->config->getAssetDir());

		if (!($path = $this->getPublicPath($linkedFile->getPathname()))) {
			$newName = AssetProvider::formatAssetFileName($linkedFile);
			$cachePathname = $assetAbsolute . "/" . $newName;
			if (!file_exists($cachePathname) || filemtime($cachePathname) !== $linkedFile->getMTime()) {
				copy($linkedFile->getPathname(), $cachePathname);
				touch($cachePathname, $linkedFile->getMTime());
			}
			$linkedFile = new \SplFileInfo($cachePathname);
			$fileInfo->setFileLink($linkedFile);
			$this->getPublicPath($linkedFile->getPathname());
			$path = $this->getPublicPath($cachePathname);
		}

		$url = clone $this->request->getUrl();
		$url->setQuery("");
		$url->setPath($path);

		if (isset($modifier)) {
			$modifier->linking($this, $url, $fileInfo, $args);
		}

		return $url;
	}

	/**
	 * @param string $file
	 * @return string|null
	 */
	private function getPublicPath(string $file): ?string
	{
		$wwwAbsoluteDir = $this->getAbsolutePath($this->config->getWwwDir());
		$fileAbsoluteDir = $this->getAbsolutePath($file);
		$wwwLength = strlen($wwwAbsoluteDir);
		if (substr($fileAbsoluteDir, 0, $wwwLength) === $wwwAbsoluteDir) {
			return substr($fileAbsoluteDir, $wwwLength);
		}
		return NULL;
	}

	/**
	 * @param string $path
	 * @return string
	 */
	private function getAbsolutePath($path): string
	{
		$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
		$parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
		$absolutes = [];
		foreach ($parts as $part) {
			if ('.' == $part) continue;
			if ('..' == $part) {
				array_pop($absolutes);
			} else {
				$absolutes[] = $part;
			}
		}
		return DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $absolutes);
	}

	/**
	 * @param string $alias
	 * @param string $serviceName
	 */
	public function addModifier(string $alias, string $serviceName): void
	{
		$this->modifiers[$alias] = $serviceName;
	}

	/**
	 * @param string $name
	 * @return IModifier|null
	 */
	public function getModifier(string $name): ?IModifier
	{
		if (array_key_exists($name, $this->modifiers)) {
			$serviceName = $this->modifiers[$name];
			return $this->container->getService($serviceName);
		}
		return NULL;
	}

	/**
	 * @return IModifier[]
	 */
	public function getModifiers(): array
	{
		return $this->modifiers;
	}

	/**
	 * @return Config
	 */
	public function getConfig(): Config
	{
		return $this->config;
	}

	/**
	 * @param \SplFileInfo $fileInfo
	 * @param $parameters
	 * @return string
	 */
	public static function formatAssetFileName(\SplFileInfo $fileInfo, array $parameters = []): string
	{
		$fileName = hash("crc32b", $fileInfo->getPath());
		foreach ($parameters as $value) {
			$fileName .= "-" . $value;
		}
		$fileName .= "-" . $fileInfo->getFilename();
		return $fileName;
	}

}