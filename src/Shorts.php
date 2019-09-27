<?php


namespace Dakujem;


use LogicException;

/**
 * Shorts
 */
class Shorts
{

    function reduce(string $full, int $limit): string
    {
        if ($limit < 1) {
            throw new LogicException('You may have slipped...');
        }
        $len = strlen($full);
        if ($len <= $limit) {
            return $full;
        }

        $ps = $this->explode($full);
        $c = count($ps);
        $i = 0;
        do {
            $ps[$i] = $ps[$i][0];
            var_dump($ps);
            $res = $this->implode($ps);
            var_dump($res);
            $i += 1;
            if ($i === $c) {
                break;
            }
        } while (strlen($res) > $limit);

        return strlen($res) <= $limit ? $res : substr($this->initials($full), -$limit);
    }


    function initials(string $full): string
    {
        return implode('', array_map(function ($p) {
            return $p[0];
        }, $this->explode($full)));
    }


    /**
     * Don't fancy static methods, prefer instances.
     *
     * @param mixed ...$args
     * @return self
     */
    static function i(...$args): self
    {
        return new static(...$args);
    }


    /**
     * @internal
     */
    protected function explode(string $string): array
    {
        return array_values(array_filter(preg_split('/\W+/u', $string)));
    }


    protected function implode(array $p): string
    {
        // glue with `. ` and add a `.` if the last name was abbreviated too
        //TODO this fails if the order is reversed
        return implode('. ', $p) . (strlen($p[count($p) - 1]) === 1 ? '.' : '');
    }

}