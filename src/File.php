<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Asset;

use Chomenko\Asset\Exceptions\AssetsException;

class File
{

	/**
	 * @var string|AssetFile|\SplFileInfo
	 */
	private $origin;

	/**
	 * @var \SplFileInfo
	 */
	private $fileInfo;

	/**
	 * @var \SplFileInfo
	 */
	private $fileLink;

	/**
	 * @param string|AssetFile|\SplFileInfo $file
	 * @param bool $throw
	 * @throws AssetsException
	 */
	public function __construct($file, bool $throw = TRUE)
	{
		$this->origin = $file;
		if (is_object($file) && !$file instanceof AssetFile && !$file instanceof \SplFileInfo) {
			throw AssetsException::fileMustInstance($file);
		}

		if (is_object($file) && $file instanceof AssetFile) {
			$this->fileInfo = $file->getFileInfo();
		}

		if (is_object($file) && $file instanceof \SplFileInfo) {
			$this->fileInfo = $file;
		}

		if (is_string($file)) {
			if (!file_exists($file) && $throw) {
				throw AssetsException::fileDoesNotExist($file);
			}
			$this->fileInfo = new \SplFileInfo($file);
		}
		$this->fileLink = $this->fileInfo;
	}

	/**
	 * @return AssetFile|\SplFileInfo|string
	 */
	public function getOrigin()
	{
		return $this->origin;
	}

	/**
	 * @return \SplFileInfo
	 */
	public function getFileInfo(): \SplFileInfo
	{
		return $this->fileInfo;
	}

	/**
	 * @param \SplFileInfo $fileInfo
	 */
	public function setFileInfo(\SplFileInfo $fileInfo): void
	{
		$this->fileInfo = $fileInfo;
	}

	/**
	 * @return \SplFileInfo
	 */
	public function getFileLink(): \SplFileInfo
	{
		return $this->fileLink;
	}

	/**
	 * @param \SplFileInfo $fileLink
	 */
	public function setFileLink(\SplFileInfo $fileLink): void
	{
		$this->fileLink = $fileLink;
	}

}
