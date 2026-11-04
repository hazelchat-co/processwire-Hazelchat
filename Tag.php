<?php

/*
 * This file is part of Hazelchat for ProcessWire.
 *
 * (C) 2026 Hazelchat. MIT License; see LICENSE.
 */

namespace Hazelchat\ProcessWire;

/**
 * The one script tag and where it goes, as plain functions with no
 * ProcessWire dependency, so they can be tested with PHP alone.
 *
 * Three forms, one per consent setting: the plain tag; the tag that waits in
 * the browser for a consent manager the chat knows (data-require-consent);
 * and PrivacyWire's own inert form, which PrivacyWire turns into a working
 * script once the visitor allows Functional cookies (it keeps the id and
 * every data- attribute, and takes src from data-src).
 */
final class Tag
{
    /** The only address this module ever prints. */
    const SCRIPT_URL = 'https://app.hazelchat.co/widget.js';

    /** Hazelchat counts installations of this module by this id. */
    const SCRIPT_ID = 'hazelchat-processwire';

    const PLAIN = 'plain';
    const CONSENT = 'consent';
    const PRIVACYWIRE = 'privacywire';

    /** The PrivacyWire group the chat belongs to. */
    const PRIVACYWIRE_GROUP = 'functional';

    /**
     * The script tag, or null when nothing may be printed. No inline code,
     * no data-platform and no address but SCRIPT_URL.
     */
    public static function tag(?string $key, string $mode = self::PLAIN): ?string
    {
        if (!Key::valid($key)) {
            return null;
        }
        if ($mode === self::PRIVACYWIRE) {
            return '<script type="text/plain" data-type="text/javascript" data-category="' . self::PRIVACYWIRE_GROUP . '" data-src="' . self::SCRIPT_URL
                . '" class="require-consent" id="' . self::SCRIPT_ID . '" defer data-agent="' . $key . '"></script>';
        }

        return '<script src="' . self::SCRIPT_URL . '" id="' . self::SCRIPT_ID . '" defer data-agent="' . $key . '"'
            . ($mode === self::CONSENT ? ' data-require-consent="true"' : '')
            . '></script>';
    }

    /**
     * Which form the page gets.
     *
     * @param bool $wait the owner's "Wait for my consent manager" switch
     * @param bool $privacyWire PrivacyWire is installed and switched on
     */
    public static function mode(bool $wait, bool $privacyWire): string
    {
        if (!$wait) {
            return self::PLAIN;
        }

        return $privacyWire ? self::PRIVACYWIRE : self::CONSENT;
    }

    /**
     * Puts the tag just before the page's last </body>. Output without
     * </body> (a fragment, JSON) or one that already carries the tag stays as
     * it is. Only a real </body> counts: </bodyx> does not.
     */
    public static function insert(string $html, ?string $tag): string
    {
        if ($tag === null || preg_match('/\bid\s*=\s*["\']?' . self::SCRIPT_ID . '["\'\s>]/i', $html) === 1) {
            return $html;
        }
        if (preg_match_all('/<\/body\s*>/i', $html, $found, PREG_OFFSET_CAPTURE) < 1) {
            return $html;
        }
        $at = $found[0][\count($found[0]) - 1][1];

        return substr($html, 0, $at) . $tag . "\n" . substr($html, $at);
    }
}
