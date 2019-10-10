<?php


namespace Dakujem;

define('ROOT', __DIR__);

require_once ROOT . '/../vendor/autoload.php';

use Tester\Assert;
use Tester\Environment;
use Tester\TestCase;

// tester + errors
Environment::setup();


class ShortsBaseTest extends TestCase
{

    protected function assertLimit(string $expected, string $input, ...$args)
    {
        // all of these 3 should produce exactly the same output
        Assert::same($expected, Shorts::cap($input, ...$args));
        Assert::same($expected, Shorts::i()->limit($input, ...$args));
        Assert::same($expected, Shorts::i()->limiter(...$args)($input));
    }

    protected function assertReduce(string $expected, string $input, ...$args)
    {
        // all of these 3 should produce exactly the same output
        Assert::same($expected, Shorts::shrink($input, ...$args));
        Assert::same($expected, Shorts::i()->reduce($input, ...$args));
        Assert::same($expected, Shorts::i()->reducer(...$args)($input));
    }

    protected function assertInitials(string $expected, string $input, ...$args)
    {
        // all of these 3 should produce exactly the same output
        Assert::same($expected, Shorts::initials($input, ...$args));
        Assert::same($expected, Shorts::i()->toInitials($input, ...$args));
        Assert::same($expected, Shorts::i()->initialsFormatter(...$args)($input));
    }

}


