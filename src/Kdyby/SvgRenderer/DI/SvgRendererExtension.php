<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008 Filip Procházka (filip@prochazka.su)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Kdyby\SvgRenderer\DI;

use Kdyby;
use Nette;
use Nette\Utils\Validators;



/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class SvgRendererExtension extends Nette\Config\CompilerExtension
{

	/**
	 * @var array
	 */
	public $defaults = array(
		'provider' => 'http://svg-png.kdyby.org',
		'apiKey' => NULL,
	);



	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$builder->addDefinition($this->prefix('config'))
			->setClass('Kdyby\SvgRenderer\DI\Configuration')
			->addSetup('$provider', array($config['provider']))
			->addSetup('$apiKey', array($config['apiKey']));

		$builder->addDefinition($this->prefix('storage'))
			->setClass('Kdyby\SvgRenderer\SvgStorage', array($builder->expand('%tempDir%/cache')));

		$generator = $builder->addDefinition($this->prefix('renderer'))
			->setClass('Kdyby\SvgRenderer\IRenderer');

		if (!empty($config['apiKey'])) {
			$generator->setFactory('Kdyby\SvgRenderer\RemoteRenderer');

		} else {
			$generator->setFactory('Kdyby\SvgRenderer\InkscapeRenderer');
		}
	}



	public function beforeCompile()
	{
		$config = $this->getConfig($this->defaults);
		if (!empty($config['apiKey'])) {
			$configuration = new Configuration();
			$configuration->apiKey = $config['apiKey'];
			$configuration->provider = $config['provider'];

			if ($configuration->testConnection() === FALSE) {
				trigger_error("The Svg Renderer is badly configured or components are missing. Please check log.", E_USER_WARNING);
			}
		}
	}



	/**
	 * @param \Nette\Config\Configurator $configurator
	 */
	public static function register(Nette\Config\Configurator $configurator)
	{
		$configurator->onCompile[] = function ($config, Nette\Config\Compiler $compiler) {
			$compiler->addExtension('svgRenderer', new SvgRendererExtension());
		};
	}

}

