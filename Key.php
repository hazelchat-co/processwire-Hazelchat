<?php

/*
 * This file is part of Hazelchat for ProcessWire.
 *
 * (C) 2026 Hazelchat. MIT License; see LICENSE.
 */

namespace Hazelchat\ProcessWire;

/**
 * The public widget key, as plain functions with no ProcessWire dependency,
 * so they can be tested with PHP alone. The key is public: it names an agent
 * and grants no access to the Hazelchat account. Written for PHP 7.3, the
 * oldest PHP this module supports.
 */
final class Key
{
    /** Room for a pasted installation code; the key itself is 64 characters. */
    const MAX_INPUT = 1000;

    /** @param mixed $key */
    public static function valid($key): bool
    {
        return \is_string($key) && preg_match('/\A[a-f0-9]{64}\z/', $key) === 1;
    }

    /**
     * The setting takes the whole installation code or just the key. A bare
     * 64-hex value is the key; otherwise the text must hold exactly one
     * data-agent attribute (outside HTML comments) whose value is 64
     * lowercase hex. Anything else gives null, and nothing is printed.
     *
     * @param mixed $value
     */
    public static function fromInput($value): ?string
    {
        if (!\is_string($value)) {
            return null;
        }
        $value = trim($value);
        if ($value === '' || \strlen($value) > self::MAX_INPUT) {
            return null;
        }
        if (self::valid($value)) {
            return $value;
        }

        // A key that is only mentioned in a comment is not the code.
        $value = preg_replace('/<!--.*?-->/s', '', $value);
        if ($value === null || strpos($value, '<!--') !== false) {
            return null;
        }

        // Exactly one data-agent attribute; look-alikes such as
        // xdata-agent or data-agent-x are not it, and two are ambiguous.
        $attribute = '(?<![A-Za-z0-9_:-])data-agent(?![A-Za-z0-9_:-])\s*=';
        if (preg_match_all('/' . $attribute . '/i', $value) !== 1) {
            return null;
        }
        if (preg_match('/' . $attribute . '\s*(?:"([a-f0-9]{64})"|\'([a-f0-9]{64})\'|([a-f0-9]{64})(?=[\s>\/]|\z))/', $value, $match) !== 1) {
            return null;
        }

        $key = ($match[1] ?? '') . ($match[2] ?? '') . ($match[3] ?? '');

        return self::valid($key) ? $key : null;
    }
}
