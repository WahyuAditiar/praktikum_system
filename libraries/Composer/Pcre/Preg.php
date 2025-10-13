<?php
namespace Composer\Pcre;

class Preg
{
    public static function isMatch(string $pattern, string $subject): bool
    {
        return (bool) preg_match($pattern, $subject);
    }

    public static function match(string $pattern, string $subject, ?array &$matches = null, int $flags = 0, int $offset = 0): int
    {
        return preg_match($pattern, $subject, $matches, $flags, $offset);
    }

    public static function matchAll(string $pattern, string $subject, ?array &$matches = null, int $flags = 0, int $offset = 0): int
    {
        return preg_match_all($pattern, $subject, $matches, $flags, $offset);
    }

    public static function replace(string|array $pattern, string|array $replacement, string|array $subject, int $limit = -1, ?int &$count = null): string|array|null
    {
        return preg_replace($pattern, $replacement, $subject, $limit, $count);
    }

    public static function replaceCallback(string|array $pattern, callable $replacement, string|array $subject, int $limit = -1, ?int &$count = null): string|array|null
    {
        return preg_replace_callback($pattern, $replacement, $subject, $limit, $count);
    }

    public static function split(string $pattern, string $subject, int $limit = -1, int $flags = 0): array|false
    {
        return preg_split($pattern, $subject, $limit, $flags);
    }
}
