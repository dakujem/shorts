<?php


namespace Dakujem\Shorts\Equip;


/**
 * Standard "stitcher" that glues the parts of a name into a full name string.
 */
class ShortsStitcher
{

	private $glue;
	private $suffix;


	public function __construct(string $suffix = '.', string $glue = ' ')
	{
		$this->suffix = $suffix;
		$this->glue = $glue;
	}


	public function __invoke(array $parts): string
	{

		// todo tu bude potreben dorobit logiku, ktora spoji inicialy inak, alebo bude musiet pouzivat dva implodery

		return implode($this->glue, array_map(function (string $p): string {
			return $p . (strlen($p) === 1 ? $this->suffix : '');
		}, $parts));
	}

}
