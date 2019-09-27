<?php

require_once 'bootstrap.php';

use Dakujem\Shorts;
use Tester\Assert;
use Tester\TestCase;


final class ShortsTest extends TestCase
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
    }


    function testInitials()
    {
        $s = new Shorts();
        Assert::same('FB', $s->initials('Foo Bar'));
        Assert::same('HVR', $s->initials('Hugo Ventil Rypák'));
        Assert::same('PEEG', $s->initials('Pablo Emilio Escobar Gaviria'));
    }

    /**
     * So i want this
     * foo bar -> f. bar
     * foo bar -> foo b.
     * foo bar -> f. b.
     * foo bar -> f.b.
     * foo bar -> fb
     */
    function testShortening()
    {
        $s = new Shorts();

        // edge cases
        Assert::exception(function () use ($s) {
            $s->reduce('Foo Bar', 0);
        }, LogicException::class);
        Assert::exception(function () use ($s) {
            $s->reduce('Foo Bar', -1);
        }, LogicException::class);

        // no shortening yet
        Assert::same('Foo Bar', $s->reduce('Foo Bar', 10)); // no shortening, the name is shorter than the limit
        Assert::same('Foo Bar', $s->reduce('Foo Bar', strlen('Foo Bar'))); // no shortening still

        // yeah, finally some shortening
        Assert::same('F. Bar', $s->reduce('Foo Bar', 6)); // -> "F. Bar"
        Assert::same('F. B.', $s->reduce('Foo Bar', 5)); // -> "F. B."

        // too short, results in initials
        Assert::same('FB', $s->reduce('Foo Bar', 4)); // -> "FB"
        Assert::same('FB', $s->reduce('Foo Bar', 2)); // -> "FB"
        Assert::same('B', $s->reduce('Foo Bar', 1)); // -> "B"
    }

    /**
     * I want the reduceing to adapt to the length constraints and select which names to shorten,
     * according to name priority.
     *
     * Jean Michael Jarre -> Jean M. Jarre -> J. M. Jarre -> J. M. J.
     */
    function testLongNameShortening()
    {

    }

    /**
     * I want to be able to explicitly set the priority of which names to shorten first.
     *
     * Pablo Emilio Escobar Gaviria -> Pablo E. Escobar Gaviria -> Pablo E. Escobar G. -> P. E. Escobar G. -> hmmm, funny
     */
    function testPriority()
    {

    }

}


(new ShortsTest())->run();

