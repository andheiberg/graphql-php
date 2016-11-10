<?php

namespace GraphQL\Language;

use GraphQL\Language\AST\Location;

class Source
{
    /**
     * @var string
     */
    public $body;

    /**
     * @var int
     */
    public $length;

    /**
     * @var string
     */
    public $name;

    public function __construct($body, $name = null)
    {
        $this->body = $body;
        $this->length = mb_strlen($body, 'UTF-8');
        $this->name = $name ?: 'GraphQL';
    }

    /**
     * @param Location $position
     *
     * @return SourceLocation
     */
    public function getLocation(Location $position)
    {
        $line = 1;
        $column = $position->start + 1;

        $utfChars = json_decode('"\u2028\u2029"');
        $lineRegexp = '/\r\n|[\n\r'.$utfChars.']/su';
        $matches = [];
        preg_match_all($lineRegexp, mb_substr($this->body, 0, $position->start, 'UTF-8'), $matches, PREG_OFFSET_CAPTURE);

        foreach ($matches[0] as $index => $match) {
            $line += 1;
            $column = $position->start + 1 - ($match[1] + mb_strlen($match[0], 'UTF-8'));
        }

        return new SourceLocation($line, $column);
    }
}
