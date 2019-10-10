<?php

namespace Dakujem;

require_once 'test_suite_bootstrap.php';



final class ReducingTest extends ShortsBaseTest
{


    /**
     * foo bar -> f. bar.
     * foo bar bar -> f. b. bar
     */
    function testFirstNames()
    {
        $this->assertReduce('F. Bar', 'Foo Bar');
        $this->assertReduce('F. B. Bar', 'Foo Bar Bar');
        $this->assertReduce('f. bar', 'foo bar');
        $this->assertReduce('f. b. bar', 'foo bar bar');
        $this->assertReduce('J. R. R. Tolkien', 'John Ronald Reuel Tolkien');

        $this->assertReduce('Foo', 'Foo');
        $this->assertReduce('', '');
    }


    /**
     * foo bar -> foo b.
     * foo bar bar -> foo b. b.
     */
    function testLastNames()
    {
        $this->assertReduce('Foo B.', 'Foo Bar', Shorts::FIRST_NAME);
        $this->assertReduce('Foo B. B.', 'Foo Bar Bar', Shorts::FIRST_NAME);
        $this->assertReduce('foo b.', 'foo bar', Shorts::FIRST_NAME);
        $this->assertReduce('foo b. b.', 'foo bar bar', Shorts::FIRST_NAME);
        $this->assertReduce('John R. R. T.', 'John Ronald Reuel Tolkien', Shorts::FIRST_NAME);

        $this->assertReduce('Foo', 'Foo', Shorts::FIRST_NAME);
        $this->assertReduce('', '', Shorts::FIRST_NAME);
    }


}


(new ReducingTest())->run();

