<?php

require_once 'test_suite_bootstrap.php';

use Dakujem\Shorts;
use Tester\Assert;
use Tester\TestCase;


final class ShortsLegacyTest extends TestCase
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


	function testInitials()
	{
		$s = new Shorts();
		Assert::same('FB', $s->toInitials('Foo Bar'));
		Assert::same('HVR', $s->toInitials('Hugo Ventil Rypák'));
		Assert::same('PEEG', $s->toInitials('Pablo Emilio Escobar Gaviria'));

		Assert::same('F.B.', $s->toInitials('Foo Bar', '.'));
		Assert::same('H. V. R.', $s->toInitials('Hugo Ventil Rypák', '.', ' '));
		Assert::same('P^^-E^^-E^^-G^^', $s->toInitials('Pablo Emilio Escobar Gaviria', '^^', '-'));
	}


	/**
	 * foo bar -> foo b.
	 * foo bar bar -> foo b. b.
	 */
	function testKeepFirst()
	{
		$s = new Shorts();

		Assert::same('Foo B.', $s->keepFirst('Foo Bar'));
		Assert::same('Foo B. B.', $s->keepFirst('Foo Bar Bar'));
		Assert::same('foo b.', $s->keepFirst('foo bar'));
		Assert::same('foo b. b.', $s->keepFirst('foo bar bar'));

		Assert::same('Foo', $s->keepFirst('Foo'));
		Assert::same('', $s->keepFirst(''));
	}


	/**
	 * foo bar -> f. bar.
	 * foo bar bar -> f. b. bar
	 */
	function testKeepLast()
	{
		$s = new Shorts();

		Assert::same('F. Bar', $s->keepLast('Foo Bar'));
		Assert::same('F. B. Bar', $s->keepLast('Foo Bar Bar'));
		Assert::same('f. bar', $s->keepLast('foo bar'));
		Assert::same('f. b. bar', $s->keepLast('foo bar bar'));

		Assert::same('Foo', $s->keepLast('Foo'));
		Assert::same('', $s->keepLast(''));
	}


	/**
	 * So i want this
	 * foo bar -> f. bar
	 * foo bar -> f. b.
	 * foo bar -> f.b.
	 * foo bar -> fb
	 */
	function testReduceFirst()
	{
		$s = new Shorts();

		// edge cases
		Assert::exception(function () use ($s) {
			$s->reduceFirst('Foo Bar', 0);
		}, LogicException::class);
		Assert::exception(function () use ($s) {
			$s->reduceFirst('Foo Bar', -1);
		}, LogicException::class);

		// no shortening yet
		Assert::same('Foo Bar', $s->reduceFirst('Foo Bar', strlen('Foo Bar') + 1)); // no shortening, the name is shorter than the limit
		Assert::same('Foo Bar', $s->reduceFirst('Foo Bar', strlen('Foo Bar'))); // no shortening still

		// yeah, finally some shortening
		Assert::same('F. Bar', $s->reduceFirst('Foo Bar', strlen('Foo Bar') - 1));
		Assert::same('F. Bar', $s->reduceFirst('Foo Bar', 6)); // -> "F. Bar"

		// too short, results in initials
		Assert::same('F.B.', $s->reduceFirst('Foo Bar', 5)); // -> "F.B." // note: when only initials are returned, spaces are omitted to reduce length
		Assert::same('F.B.', $s->reduceFirst('Foo Bar', 4));
		Assert::same('FB', $s->reduceFirst('Foo Bar', 3)); // -> "FB"
		Assert::same('FB', $s->reduceFirst('Foo Bar', 2)); // -> "FB"
		Assert::same('B', $s->reduceFirst('Foo Bar', 1)); // -> "B"
	}


	function testReduceLast()
	{
		$s = new Shorts();

		// edge cases
		Assert::exception(function () use ($s) {
			$s->reduceLast('Foo Bar', 0);
		}, LogicException::class);
		Assert::exception(function () use ($s) {
			$s->reduceLast('Foo Bar', -1);
		}, LogicException::class);

		// no shortening yet
		Assert::same('Foo Bar', $s->reduceLast('Foo Bar', strlen('Foo Bar') + 1)); // no shortening, the name is shorter than the limit
		Assert::same('Foo Bar', $s->reduceLast('Foo Bar', strlen('Foo Bar'))); // no shortening still

		// yeah, finally some shortening
		Assert::same('Foo B.', $s->reduceLast('Foo Bar', strlen('Foo Bar') - 1));
		Assert::same('Foo B.', $s->reduceLast('Foo Bar', 6)); // -> "Foo B."

		// too short, results in initials
		Assert::same('F.B.', $s->reduceLast('Foo Bar', 5)); // -> "F.B." // note: when only initials are returned, spaces are omitted to reduce length
		Assert::same('F.B.', $s->reduceLast('Foo Bar', 4));
		Assert::same('FB', $s->reduceLast('Foo Bar', 3)); // -> "FB"
		Assert::same('FB', $s->reduceLast('Foo Bar', 2)); // -> "FB"
		Assert::same('B', $s->reduceLast('Foo Bar', 1)); // -> "B"
	}


	function testReduceWithMiddleNames()
	{
		$s = new Shorts();
		$fn = 'John Ronald Reuel Tolkien'; // 25 characters long
		Assert::same($fn, $s->reduceFirst($fn, 25));
		Assert::same('John R. Reuel Tolkien', $s->reduceFirst($fn, 24));
		Assert::same('John R. Reuel Tolkien', $s->reduceFirst($fn, 21));
		Assert::same('John R. R. Tolkien', $s->reduceFirst($fn, 20));
		Assert::same('John R. R. Tolkien', $s->reduceFirst($fn, 18));
		Assert::same('J. R. R. Tolkien', $s->reduceFirst($fn, 17));
		Assert::same('J. R. R. Tolkien', $s->reduceFirst($fn, 16));
		Assert::same('J.R.R. Tolkien', $s->reduceFirst($fn, 15));
		Assert::same('J.R.R. Tolkien', $s->reduceFirst($fn, 14));
		Assert::same('J. Tolkien', $s->reduceFirst($fn, 13));
		Assert::same('J. Tolkien', $s->reduceFirst($fn, 10));
		Assert::same('J.R.R.T.', $s->reduceFirst($fn, 9));
		Assert::same('J.R.R.T.', $s->reduceFirst($fn, 8));
		Assert::same('JRRT', $s->reduceFirst($fn, 7));
		Assert::same('JRRT', $s->reduceFirst($fn, 4));
		Assert::same('JT', $s->reduceFirst($fn, 3));
		Assert::same('JT', $s->reduceFirst($fn, 2));
		Assert::same('T', $s->reduceFirst($fn, 1));

		// edge case
		$e = 'John Band Ding Fu';
		Assert::same('J.B.D. Fu', $s->reduceFirst($e, 9));
		Assert::same('J.B.D.F.', $s->reduceFirst($e, 8));
		Assert::same('J. Fu', $s->reduceFirst($e, 7));
		Assert::same('J. Fu', $s->reduceFirst($e, 5));
		Assert::same('JBDF', $s->reduceFirst($e, 4));
	}


	function testReduceLastWithMiddleNames()
	{
		$s = new Shorts();
		$fn = 'John Ronald Reuel Tolkien'; // 25 characters long
		Assert::same($fn, $s->reduceLast($fn, 25));
		Assert::same('John R. Reuel Tolkien', $s->reduceLast($fn, 24));
		Assert::same('John R. Reuel Tolkien', $s->reduceLast($fn, 21));
		Assert::same('John R. R. Tolkien', $s->reduceLast($fn, 20));
		Assert::same('John R. R. Tolkien', $s->reduceLast($fn, 18));
		Assert::same('John R. R. T.', $s->reduceLast($fn, 17));
		Assert::same('John R. R. T.', $s->reduceLast($fn, 13));
		Assert::same('John R.R.T.', $s->reduceLast($fn, 12));
		Assert::same('John R.R.T.', $s->reduceLast($fn, 11));
		Assert::same('J.R.R.T.', $s->reduceLast($fn, 9)); // not the difference to the other shortening method
		Assert::same('J.R.R.T.', $s->reduceLast($fn, 8));
		Assert::same('John T.', $s->reduceLast($fn, 7));
		Assert::same('JRRT', $s->reduceLast($fn, 6));
		Assert::same('JRRT', $s->reduceLast($fn, 4));
		Assert::same('JT', $s->reduceLast($fn, 3));
		Assert::same('JT', $s->reduceLast($fn, 2));
		Assert::same('T', $s->reduceLast($fn, 1));

		// edge case
		$e = 'Fu Jong Band Ding';
		Assert::same('Fu J.B.D.', $s->reduceLast($e, 9));
		Assert::same('F.J.B.D.', $s->reduceLast($e, 8));
		Assert::same('Fu D.', $s->reduceLast($e, 7));
		Assert::same('Fu D.', $s->reduceLast($e, 5));
		Assert::same('FJBD', $s->reduceLast($e, 4));
	}


	function experimentalApi()
	{

		Shorts::cap('foo bar', 20);
		Shorts::i()->limit('foo bar', 20);
		Shorts::i()->limit('foo bar', 20, Shorts::FIRST_NAME);
		Shorts::i()->limiter(20, Shorts::FIRST_NAME)('foo bar'); // in,run, go, proc, for, f, l

		Shorts::shrink('foo bar');
		Shorts::i()->reduce('foo bar');
		Shorts::i()->reduce('foo bar', Shorts::FIRST_NAME);
		Shorts::i()->reducer(Shorts::FIRST_NAME)('foo bar'); // in ?

		Shorts::initials('foo bar'); // fb
		Shorts::initials('foo bar', '.', ' '); // f. b.
		Shorts::i()->toInitials('foo bar'); // fb
		Shorts::i()->toInitials('foo bar', '.', ' '); // f. b.
		Shorts::i()->initialsFormatter('.', ' ')('foo bar');

		$splitter = function (string $input): array {
			return $output;
		};
		$imploder = function (array $names): string {
			return $fullName;
		};
		Shorts::i($splitter, $imploder); // + constructor


//		$foo = new class()
//		{
//
//			function bar()
//			{
//				return 'instance call';
//			}
//
//
//			public static function __callStatic($name, $arguments)
//			{
//				if ($name === 'bar') {
//					return (new self)->bar() . ' called via static';
//				} else throw new Exception('bad call: ' . $name);
//			}
//
//		};
//
//		$foo::bar();

	}

//    /**
//     * I want to be able to explicitly set the priority of which names to shorten first.
//     *
//     * Pablo Emilio Escobar Gaviria -> Pablo E. Escobar Gaviria -> Pablo E. Escobar G. -> P. E. Escobar G. -> hmmm, funny
//     */
//    function testPriority()
//    {
//
//    }

}


(new ShortsLegacyTest())->run();

