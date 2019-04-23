<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Asset\Modifiers;

use Chomenko\Asset\AssetProvider;
use Chomenko\Asset\File;
use Nette\Http\Url;

interface IModifier
{

	/**
	 * @param AssetProvider $provider
	 * @param File $file
	 * @param array $args
	 */
	public function modify(AssetProvider $provider, File $file, array $args): void;

	/**
	 * @param AssetProvider $provider
	 * @param Url $url
	 * @param File $file
	 * @param array $args
	 */
	public function linking(AssetProvider $provider, Url $url, File $file, array $args): void;

}