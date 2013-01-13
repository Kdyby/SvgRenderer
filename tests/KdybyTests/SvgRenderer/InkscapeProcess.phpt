<?php

/**
 * Test: Kdyby\SvgRenderer\InkscapeProcess.
 *
 * @testCase Kdyby\SvgRenderer\InkscapeProcessTest
 * @author Filip Procházka <filip@prochazka.su>
 * @package Kdyby\SvgRenderer
 */

namespace KdybyTests\SvgRenderer;

use Kdyby\SvgRenderer\InkscapeProcess;
use Nette;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';



/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class InkscapeProcessTest extends Tester\TestCase
{

	public function testBuildCommand()
	{
		$process = new InkscapeProcess(array(
			'--file' => __FILE__,
			'--export-png' => __FILE__,
			'--without-gui'
		));
		Assert::match("inkscape --file='%a%' --export-png='%a%' --without-gui", $process->buildCommand());
	}

}

\run(new InkscapeProcessTest());
