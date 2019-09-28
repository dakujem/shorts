<?php


namespace Dakujem;


use LogicException;
use RuntimeException;

/**
 * Shorts
 */
class Shorts
{

    /**
     * Reduce a full name by shortening names to initials,
     * so that the length of the result does not exceed given limit.
     *
     * "John Ronald Reuel Tolkien"
     *      -> "John R. Reuel Tolkien"
     *      -> "John R. R. Tolkien"
     *      -> "J. R. R. Tolkien"
     *      -> "J.R.R. Tolkien"
     *      -> "J. Tolkien"
     *      -> "J.R.R.T."
     *      -> "JRRT"
     *      -> "JT"
     *      -> "T"
     *
     *
     * @param string $full a full person name
     * @param int    $limit
     * @return string
     */
    function reduceFirst(string $full, int $limit): string
    {
        return $this->limitNameTo($full, $limit, function (array $parts): array {
            return [
                $parts[count($parts) - 1], // last name
                $parts[0], // fist name
            ];
        }, [$this, 'significantLastName']);
    }


    function reduceLast(string $full, int $limit): string
    {
        return $this->limitNameTo($full, $limit, function (array $parts): array {
            return [
                $parts[0], // fist name
                $parts[count($parts) - 1], // last name
            ];
        }, [$this, 'significantFirstName']);
    }


    function limitNameTo(string $full, int $limit, callable $significant, callable $subroutine)
    {
        if ($limit < 1) {
            throw new LogicException('You may have slipped...');
        }
        $name = trim($full);
        $len = strlen($name);
        if ($len <= $limit) {
            // no need to do anything since the required max length is shorter than the original string
            return $name;
        }

        $parts = $this->explode($full);
        $num = count($parts);

        $significantNames = call_user_func($significant, $parts);
        $mostSignificantNameLength = strlen($significantNames[0]);

        if (
            $num > 1 && // Note: if there was only one word ($num===1), it would have been returned above
            $mostSignificantNameLength < $limit && // if the most significant name is not shorter than the limit initials will have to be used anyway
            (2 * $num <= $limit || $mostSignificantNameLength + 3 <= $limit) // the initials in the shortest form, have a dot between them; omission of middle names
        ) {
            // first try to reduce the first and middle names
            $middleNames = array_slice($parts, 1, -1);
            $target = $len - $limit; // this is the targeted minimum reduction needed for the shortener to be successful

            $candidate = call_user_func($subroutine, $limit, $target, $significantNames, $middleNames);
            if ($candidate !== null) {
                return $candidate;
            }
        }

        // fall back to using initials
        return $this->_limitInitials($parts, $limit);
    }


    private function significantLastName(int $limit, int $minimalReduction, array $significantNames, array $middleNames): ?string
    {
        $lastName = $significantNames[0];
        $firstName = $significantNames[1];

        // move the first name to the end, so that it is reduced last
        [$reduced, $reduction] = $this->reduce(array_merge($middleNames, [$firstName]), $minimalReduction);
        // move the first name back to the beginning
        $tmp = array_pop($reduced);
        array_unshift($reduced, $tmp);
        if ($reduction >= $minimalReduction) {
            // success, the actual length reduction is greater or equal to the targeted reduction
            return $this->implode(array_merge($reduced, [$lastName]));
        }

        $attempt1 = $this->implode($reduced, '.', '') . ' ' . $lastName; // J.R.R. Tolkien
        if (strlen($attempt1) <= $limit) {
            return $attempt1;
        }

        // at this point we need to try these two and see which one to return
        $attempt2 = $reduced[0] . '. ' . $lastName; // J. Tolkien (middle names omitted)
        $attempt3 = $this->implode(array_merge($reduced, [$this->_initials([$lastName])]), '.', ''); // J.R.R.T. (initials only)
        $l2 = strlen($attempt2);
        $l3 = strlen($attempt3);

        if ($l2 <= $limit && $l3 > $limit) {
            return $attempt2;
        }
        if ($l3 <= $limit && $l2 > $limit) {
            return $attempt3;
        }
        if ($l2 <= $limit && $l3 <= $limit) {
            return $l2 >= $l3 ? $attempt2 : $attempt3;
        }
        throw new LogicException('wtf'); // this should never happen
    }

    private function significantFirstName(int $limit, int $minimalReduction, array $significantNames, array $middleNames): ?string
    {
        $firstName = $significantNames[0];
        $lastName = $significantNames[1];

        // move the first name to the end, so that it is reduced last
        [$reduced, $reduction] = $this->reduce(array_merge($middleNames, [$lastName]), $minimalReduction);
        if ($reduction >= $minimalReduction) {
            // success, the actual length reduction is greater or equal to the targeted reduction
            return $this->implode(array_merge([$firstName], $reduced));
        }

        $attempt1 = $firstName . ' ' . $this->implode($reduced, '.', ''); // John R.R.T.
        if (strlen($attempt1) <= $limit) {
            return $attempt1;
        }

        // at this point we need to try these two and see which one to return
        $attempt2 = $firstName . ' ' . $reduced[count($reduced) - 1] . '.'; // John T. (middle names omitted)
        $attempt3 = $this->implode(array_merge([$this->_initials([$firstName])], $reduced), '.', ''); // J.R.R.T. (initials only)
        $l2 = strlen($attempt2);
        $l3 = strlen($attempt3);

        if ($l2 <= $limit && $l3 > $limit) {
            return $attempt2;
        }
        if ($l3 <= $limit && $l2 > $limit) {
            return $attempt3;
        }
        if ($l2 <= $limit && $l3 <= $limit) {
            return $l2 >= $l3 ? $attempt2 : $attempt3;
        }
        throw new LogicException('wtf'); // this should never happen
    }


    private function reduce(array $parts, int $minimalReduction): array
    {
        $result = [];
        $reduction = 0;
        foreach ($parts as $part) {
            if ($reduction < $minimalReduction) {
                $reduction += strlen($part) - 2; // 2 === strlen('A.')
                $result[] = $part[0];
            } else {
                $result[] = $part;
            }
        }
//        var_dump($result);
//        var_dump($reduction);
        return [$result, $reduction];
    }


    /**
     * Reduce a full name to to initials keeping the first name intact.
     *
     * "Hugo Ventil" -> "Hugo V."
     * "John Ronald Reuel Tolkien" -> "John R. R. T."
     *
     * @param string $full
     * @return string
     */
    function keepFirst(string $full, string $suffix = '.', string $glue = ' '): string
    {
        if ($full === '') {
            return '';
        }
        $parts = $this->explode($full);
        $first = array_shift($parts);
        return $this->implode(array_merge([$first], array_map(function (string $p): string {
            return $p[0]; // return the first letter, the initial
        }, $parts)), $suffix, $glue);
    }


    /**
     * Reduce a full name to to initials keeping the last name intact.
     *
     * "Hugo Ventil" -> "H. Ventil"
     * "John Ronald Reuel Tolkien" -> "J. R. R. Tolkien"
     *
     * @param string $full
     * @return string
     */
    function keepLast(string $full, string $suffix = '.', string $glue = ' '): string
    {
        if ($full === '') {
            return '';
        }
        $parts = $this->explode($full);
        $last = array_pop($parts);
        return $this->implode(array_merge(array_map(function (string $p): string {
            return $p[0]; // return the first letter, the initial
        }, $parts), [$last]), $suffix, $glue);
    }


    /**
     * Reduce a full name to initials.
     *
     * "Hugo Ventil" -> "HV" / "H. V."
     * "John Ronald Reuel Tolkien" -> "JRRT" / "J. R. R. T."
     *
     * @param string $full   a full person name to be turned into initials
     * @param string $suffix suffix added to each produced initial,
     *                       usually this would be empty to produce "AB" or a dot to produce "A. B."
     * @param string $glue   glue to put the initials together,
     *                       usually an empty string to produce "AB" or a space to produce "A. B."
     * @return string
     */
    function initials(string $full, string $suffix = '', string $glue = ''): string
    {
        return $this->_initials($this->explode($full), $suffix, $glue);
    }

    private function _initials(array $parts, string $suffix = '', string $glue = ''): string
    {
        return $this->implode(array_map(function (string $p): string {
            return $p[0]; // return the first letter, the initial
        }, $parts), $suffix, $glue);
    }


    function limitInitials(string $full, int $limit): string
    {
        if ($limit < 1) {
            throw new LogicException('You may have slipped...');
        }
        $parts = $this->explode($full);
        return $this->_limitInitials($parts, $limit);
    }

    private function _limitInitials(array $parts, int $limit)
    {
        $num = count($parts);

        // special case first
        if ($limit < $num) {
            if ($limit === 1) {
                // return the first letter of the last name only
                return $this->_initials([
                    $parts[$num - 1],
                ]);
            }
            // omit middle names
            return $this->_initials([
                $parts[0],
                $parts[$num - 1],
            ]);
        }

        // then the usual initials
        return $this->_initials($parts);
    }

    /**
     * Shorthand to create an instance.
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
     * Explode the name into parts.
     *
     * // todo and order the parts according to the priority, in ascending order.
     *
     * @internal
     *
     * @param string $input
     * @return array
     */
    protected function explode(string $input): array
    {
        // note the `u` in the regexp below for Unicode support
        return array_values(array_filter(preg_split('/\W+/u', $input), function (string $s): bool {
            return $s !== '';
        }));
    }


    /**
     * Put the parts together.
     * To each initial add a dot ($suffix), and glue all parts together with a space ($glue).
     * @internal
     *
     * @param string[] $parts
     * @param string   $suffix suffix added to each produced initial,
     *                         usually this would be empty to produce "AB" or a dot to produce "A. Bee"
     * @param string   $glue   glue to put the parts together,
     *                         usually an empty string to produce "AB" or a space to produce "A. Bee"
     * @return string
     */
    protected function implode(array $parts, string $suffix = '.', string $glue = ' '): string
    {
        return implode($glue, array_map(function (string $p) use ($suffix): string {
            return $p . (strlen($p) === 1 ? $suffix : '');
        }, $parts));
    }

}