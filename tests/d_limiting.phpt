<?php

namespace Dakujem;

require_once 'test_suite_bootstrap.php';

use LogicException;
use Tester\Assert;


final class LimitingTest extends ShortsBaseTest
{

    function testEdgeCases(){

        Assert::exception(function () {
            Shorts::i()->limit('Foo Bar', 0);
        }, LogicException::class);
        Assert::exception(function () {
            Shorts::i()->limit('Foo Bar', -1);
        }, LogicException::class);
        Assert::exception(function () {
            Shorts::cap('Foo Bar', 0);
        }, LogicException::class);
        Assert::exception(function () {
            Shorts::cap('Foo Bar', -1);
        }, LogicException::class);
        Assert::exception(function () {
            Shorts::i()->limiter(0)('Foo Bar');
        }, LogicException::class);
        Assert::exception(function () {
            Shorts::i()->limiter(-1)('Foo Bar');
        }, LogicException::class);

        Assert::exception(function () {
            Shorts::i()->limit('Foo Bar', 0, Shorts::FIRST_NAME);
        }, LogicException::class);
        Assert::exception(function () {
            Shorts::i()->limit('Foo Bar', -1, Shorts::FIRST_NAME);
        }, LogicException::class);
        Assert::exception(function () {
            Shorts::cap('Foo Bar', 0, Shorts::FIRST_NAME);
        }, LogicException::class);
        Assert::exception(function () {
            Shorts::cap('Foo Bar', -1, Shorts::FIRST_NAME);
        }, LogicException::class);
        Assert::exception(function () {
            Shorts::i()->limiter(0, Shorts::FIRST_NAME)('Foo Bar');
        }, LogicException::class);
        Assert::exception(function () {
            Shorts::i()->limiter(-1, Shorts::FIRST_NAME)('Foo Bar');
        }, LogicException::class);

    }


    function testBasicFirstNames()
    {
        // no shortening yet
        $this->assertLimit('Foo Bar', 'Foo Bar', strlen('Foo Bar') + 1);// no shortening, the name is shorter than the limit
        $this->assertLimit('Foo Bar', 'Foo Bar', strlen('Foo Bar')); // no shortening still

        // yeah, finally some shortening
        $this->assertLimit('F. Bar', 'Foo Bar', strlen('Foo Bar') - 1);
        $this->assertLimit('F. Bar', 'Foo Bar', 6); // -> "F. Bar"

        // too short, results in initials
        $this->assertLimit('F.B.', 'Foo Bar', 5); // -> "F.B." // note: when only initials are returned, spaces are omitted to reduce length
        $this->assertLimit('F.B.', 'Foo Bar', 4);
        $this->assertLimit('FB', 'Foo Bar', 3); // -> "FB"
        $this->assertLimit('FB', 'Foo Bar', 2); // -> "FB"
        $this->assertLimit('B', 'Foo Bar', 1); // -> "B"

        // confirm that the default $priority parameter value is "last name"
        $this->assertLimit('F. Bar', 'Foo Bar', strlen('Foo Bar') - 1, Shorts::LAST_NAME);
    }


    function testBasicLastNames()
    {
        // no shortening yet
        $this->assertLimit('Foo Bar', 'Foo Bar', strlen('Foo Bar') + 1, Shorts::FIRST_NAME); // no shortening, the name is shorter than the limit
        $this->assertLimit('Foo Bar', 'Foo Bar', strlen('Foo Bar'), Shorts::FIRST_NAME); // no shortening still

        // yeah, finally some shortening
        $this->assertLimit('Foo B.', 'Foo Bar', strlen('Foo Bar') - 1, Shorts::FIRST_NAME);
        $this->assertLimit('Foo B.', 'Foo Bar', 6, Shorts::FIRST_NAME); // -> "Foo B."

        // too short, results in initials
        $this->assertLimit('F.B.', 'Foo Bar', 5, Shorts::FIRST_NAME); // -> "F.B." // note: when only initials are returned, spaces are omitted to reduce length
        $this->assertLimit('F.B.', 'Foo Bar', 4, Shorts::FIRST_NAME);
        $this->assertLimit('FB', 'Foo Bar', 3, Shorts::FIRST_NAME); // -> "FB"
        $this->assertLimit('FB', 'Foo Bar', 2, Shorts::FIRST_NAME); // -> "FB"
        $this->assertLimit('B', 'Foo Bar', 1, Shorts::FIRST_NAME); // -> "B"
    }


    function testComplexLastNames()
    {
        $s = new Shorts();
        $fn = 'John Ronald Reuel Tolkien'; // 25 characters long
        $this->assertLimit($fn, $fn, 25);
        $this->assertLimit('John R. Reuel Tolkien', $fn, 24);
        $this->assertLimit('John R. Reuel Tolkien', $fn, 21);
        $this->assertLimit('John R. R. Tolkien', $fn, 20);
        $this->assertLimit('John R. R. Tolkien', $fn, 18);
        $this->assertLimit('J. R. R. Tolkien', $fn, 17);
        $this->assertLimit('J. R. R. Tolkien', $fn, 16);
        $this->assertLimit('J.R.R. Tolkien', $fn, 15);
        $this->assertLimit('J.R.R. Tolkien', $fn, 14);
        $this->assertLimit('J. Tolkien', $fn, 13);
        $this->assertLimit('J. Tolkien', $fn, 10);
        $this->assertLimit('J.R.R.T.', $fn, 9);
        $this->assertLimit('J.R.R.T.', $fn, 8);
        $this->assertLimit('JRRT', $fn, 7);
        $this->assertLimit('JRRT', $fn, 4);
        $this->assertLimit('JT', $fn, 3);
        $this->assertLimit('JT', $fn, 2);
        $this->assertLimit('T', $fn, 1);

        // edge case
        $e = 'John Band Ding Fu';
        $this->assertLimit('J.B.D. Fu', $e, 9);
        $this->assertLimit('J.B.D.F.', $e, 8);
        $this->assertLimit('J. Fu', $e, 7);
        $this->assertLimit('J. Fu', $e, 5);
        $this->assertLimit('JBDF', $e, 4);

        // confirm that the default $priority parameter value is "last name"
        $this->assertLimit('J. R. R. Tolkien', $fn, 17, Shorts::LAST_NAME);
    }


    function testComplexFirstNames()
    {
        $s = new Shorts();
        $fn = 'John Ronald Reuel Tolkien'; // 25 characters long
        $this->assertLimit($fn, $fn, 25, Shorts::FIRST_NAME);
        $this->assertLimit('John R. Reuel Tolkien', $fn, 24, Shorts::FIRST_NAME);
        $this->assertLimit('John R. Reuel Tolkien', $fn, 21, Shorts::FIRST_NAME);
        $this->assertLimit('John R. R. Tolkien', $fn, 20, Shorts::FIRST_NAME);
        $this->assertLimit('John R. R. Tolkien', $fn, 18, Shorts::FIRST_NAME);
        $this->assertLimit('John R. R. T.', $fn, 17, Shorts::FIRST_NAME);
        $this->assertLimit('John R. R. T.', $fn, 13, Shorts::FIRST_NAME);
        $this->assertLimit('John R.R.T.', $fn, 12, Shorts::FIRST_NAME);
        $this->assertLimit('John R.R.T.', $fn, 11, Shorts::FIRST_NAME);
        $this->assertLimit('J.R.R.T.', $fn, 9, Shorts::FIRST_NAME); // not the difference to the other shortening method
        $this->assertLimit('J.R.R.T.', $fn, 8, Shorts::FIRST_NAME);
        $this->assertLimit('John T.', $fn, 7, Shorts::FIRST_NAME);
        $this->assertLimit('JRRT', $fn, 6, Shorts::FIRST_NAME);
        $this->assertLimit('JRRT', $fn, 4, Shorts::FIRST_NAME);
        $this->assertLimit('JT', $fn, 3, Shorts::FIRST_NAME);
        $this->assertLimit('JT', $fn, 2, Shorts::FIRST_NAME);
        $this->assertLimit('T', $fn, 1, Shorts::FIRST_NAME);

        // edge case
        $e = 'Fu Jong Band Ding';
        $this->assertLimit('Fu J.B.D.', $e, 9, Shorts::FIRST_NAME);
        $this->assertLimit('F.J.B.D.', $e, 8, Shorts::FIRST_NAME);
        $this->assertLimit('Fu D.', $e, 7, Shorts::FIRST_NAME);
        $this->assertLimit('Fu D.', $e, 5, Shorts::FIRST_NAME);
        $this->assertLimit('FJBD', $e, 4, Shorts::FIRST_NAME);
    }


}


(new LimitingTest())->run();

