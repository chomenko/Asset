<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Asset;

interface AssetFile
{

	/**
	 * @return \SplFileInfo
	 */
	public function getFileInfo(): \SplFileInfo;

}