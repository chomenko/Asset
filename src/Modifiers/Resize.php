<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Asset\Modifiers;

use Chomenko\Asset\AssetProvider;
use Chomenko\Asset\Exceptions\AssetsException;
use Chomenko\Asset\File;
use Nette\Utils\Image;

class Resize extends Modifier
{

	private $flags = [
		"shrink" => Image::SHRINK_ONLY,
		"stretch" => Image::STRETCH,
		"fit" => Image::FIT,
		"fill" => Image::FILL,
		"exact" => Image::EXACT,
	];

	/**
	 * @param AssetProvider $provider
	 * @param File $file
	 * @param array $args
	 * @throws AssetsException
	 * @throws \Nette\Utils\UnknownImageFileException
	 */
	public function modify(AssetProvider $provider, File $file, array $args): void
	{
		if (!isset($args[0]) || !isset($args[1])) {
			throw new AssetsException('Modifier resize required 2 parameters $height and $width');
		}

		$width = $args[0];
		$height = $args[1];
		$fileInfo = $file->getFileInfo();
		$flag = isset($args[2]) ? $args[2] : Image::EXACT;
		$assetDir = $provider->getConfig()->getAssetDir();

		if (array_key_exists($flag, $this->flags)) {
			$flag = $this->flags[$flag];
		}

		$fileName = AssetProvider::formatAssetFileName($fileInfo, [
			$width . "x" . $height,
			$flag,
		]);

		$filePathname = $assetDir . "/" . $fileName;

		if (!file_exists($filePathname) || filemtime($filePathname) !== $fileInfo->getMTime()) {
			$ext = strtolower(pathinfo($fileInfo, PATHINFO_EXTENSION));
			switch ($ext) {
				case $ext == "jpeg" || $ext == "jpg":
					$type = Image::JPEG;
					break;
				case "gif":
					$type = Image::GIF;
					break;
				default:
					$type = Image::PNG;
			}

			$image = Image::fromFile($fileInfo);
			$image->resize($width, $height, $flag);
			$image->save($filePathname, 100, $type);
			touch($filePathname, $fileInfo->getMTime());
		}

		$file->setFileLink(new \SplFileInfo($filePathname));
	}

}