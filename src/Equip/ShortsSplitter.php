<?php


namespace Dakujem\Shorts\Equip;


/**
 * Standard splitter, explodes a full name into parts.
 */
class ShortsSplitter
{

	public function __invoke(string $fullName): array
	{
		// note the `u` in the regexp below for Unicode support
		return array_values(array_filter(preg_split('/\W+/u', $fullName), function (string $s): bool {
			return $s !== '';
		}));
	}

}
