<?php
namespace Composer\Pcre;

class PcreException extends \RuntimeException
{
    public static function fromFunction(string $function, string $pattern): self
    {
        return new self("PCRE error in {$function} with pattern: {$pattern}");
    }
}
