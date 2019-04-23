<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\Asset\Macros;

use Chomenko\Asset\DI\AssetExtension;
use Chomenko\Asset\AssetProvider;
use Latte\Compiler;
use Latte\Engine;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;

class Latte extends MacroSet
{

	private $nodeTypes = [
		"img" => "src",
		"a" => "href",
		"link" => "href",
		"script" => "src",
	];

	/**
	 * @var AssetProvider
	 */
	private $provider;

	/**
	 * @param Compiler $compiler
	 * @param AssetProvider $provider
	 */
	public function __construct(Compiler $compiler, AssetProvider $provider)
	{
		parent::__construct($compiler);
		$this->provider = $provider;
	}

	/**
	 * @param Engine $engine
	 * @param AssetProvider $provider
	 * @return Latte
	 */
	public static function install(Engine $engine, AssetProvider $provider)
	{
		$engine->addProvider(AssetExtension::KEY_PROVIDER, $provider);
		$me = new static($engine->getCompiler(), $provider);
		$me->addMacro('asset', [$me, 'macroAsset'], NULL, [$me, 'macroAttrAsset']);
		return $me;
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 * @return string
	 * @throws \Latte\CompileException
	 */
	public function macroAsset(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write('echo $this->global->{"' . AssetExtension::KEY_PROVIDER . '"}->link(%node.args)');
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 * @return string
	 * @throws \Latte\CompileException
	 */
	public function macroAttrAsset(MacroNode $node, PhpWriter $writer)
	{
		$type = "href";
		if (array_key_exists($node->htmlNode->name, $this->nodeTypes)) {
			$type = $this->nodeTypes[$node->htmlNode->name];
		}
		$write = "echo ' {$type}=\"';";
		$write .= 'echo $this->global->{"' . AssetExtension::KEY_PROVIDER . '"}->link(%node.args);';
		$write .= "echo '\"'";
		return $writer->write($write);
	}

}
