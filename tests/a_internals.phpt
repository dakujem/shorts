<?php

namespace Dakujem;

require_once 'test_suite_bootstrap.php';

use Tester\Assert;


final class InternalsTest extends ShortsBaseTest
{

	function testExploding()
	{
		$foo = new class extends Shorts
		{

			function _explode(...$args)
			{
				return $this->explode(...$args);
			}

		};

		Assert::same(['A', 'b', 'C'], $foo->_explode('A b C'));
		Assert::same(['A', 'b', 'C'], $foo->_explode('A-b&C'));
		Assert::same(['A', 'b', 'C'], $foo->_explode('A,...    b^C'));
		Assert::same(['A', 'b_C'], $foo->_explode('A-b_C'));
		Assert::same(['Hugo', 'Ventil', 'Rypák'], $foo->_explode('Hugo Ventil Rypák'));
		Assert::same(['šēēā', 'or', 'кукареку', 'Čucoriedka', 'Šedá'], $foo->_explode('šēēā or кукареку-Čucoriedka Šedá'));
		Assert::same(['けん', '劍'], $foo->_explode('けん + 劍'));
		Assert::same(['0', '0', '0000'], $foo->_explode('0 0 0000'));
	}


	function testImploding()
	{
		$foo = new class extends Shorts
		{

			function _implode(...$args)
			{
				return $this->implode(...$args);
			}

		};

		Assert::same('A. b. C.', $foo->_implode(['A', 'b', 'C']));
		Assert::same('Aa b. Cee', $foo->_implode(['Aa', 'b', 'Cee']));
		Assert::same('AbC', $foo->_implode(['A', 'b', 'C'], '', ''));
		Assert::same('A-b-C', $foo->_implode(['A', 'b', 'C'], '', '-'));
		Assert::same('A-b-C-', $foo->_implode(['A', 'b', 'C'], '-', ''));

		Assert::same('šēēā or кукареку Čucoriedka Šedá', $foo->_implode(['šēēā', 'or', 'кукареку', 'Čucoriedka', 'Šedá']));
		Assert::same('けん 劍', $foo->_implode(['けん', '劍']));
	}

}


(new InternalsTest())->run();

