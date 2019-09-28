<?php

class Pee
{
    public function __construct(string $word, int $index, bool $abbreviated = false)
    {
        $this->word = $word;
        $this->index = $index;
        $this->abbreviated = $abbreviated;
    }

    public function __toString()
    {
        return $this->word;
    }
}
