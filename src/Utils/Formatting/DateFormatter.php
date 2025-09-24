<?php

namespace App\Utils\Formatting;

use DateTime;
use IntlDateFormatter;

final class DateFormatter
{

    /** 
     * Parse a date string using multiple accepted input formats and return a DateTime.
     *
     * Accepted input formats (in order of precedence):
     *  - Y-m-d   (e.g., 2025-03-21)
     *  - d.m.Y   (e.g., 21.03.2025)
     *  - d/m/Y   (e.g., 21/03/2025)
     *
     * The leading bang in the format ('!') resets unspecified fields to sane defaults (00:00:00).
     *
     * @param string|null $input Raw date string to parse.
     * @return DateTime|null Parsed DateTime on success, or null if input is empty/invalid.
     */
    public static function parseFlexible(?string $input): ?DateTime
    {
        if (!$input) return null;
        foreach (['!Y-m-d', '!d.m.Y', '!d/m/Y'] as $fmt) {
            $dt = DateTime::createFromFormat($fmt, $input);
            if ($dt) return $dt;
        }
        return null;
    }

    /**
     * Convert a flexible date string to the UI short format 'd.m.Y'.
     * Returns null if the input cannot be parsed.
     *
     * @param string|null $input Date string in any supported input format.
     * @return string|null Date formatted as 'd.m.Y', or null on failure.
     */
    public static function toUi(?string $input): ?string
    {
        $dt = self::parseFlexible($input);
        return $dt ? $dt->format('d.m.Y') : null;
    }

    /**
     * Convert a flexible date string to the database format 'Y-m-d'.
     * Safe to use with values coming from an <input type="date">.
     *
     * @param string|null $input Date string in any supported input format.
     * @return string|null Date formatted as 'Y-m-d', or null on failure.
     */
    public static function toDb(?string $input): ?string
    {
        $dt = self::parseFlexible($input);
        return $dt ? $dt->format('Y-m-d') : null;
    }

    /**
     * Format a date string to the short UI format 'd/m/Y' (e.g., 21/03/2025).
     *
     * @param string $date Date in Y-m-d format.
     * @return string Formatted date or error message.
     */
    public static function short(string $date): ?string
    {
        $dt = self::parseFlexible($date);
        return $dt ? $dt->format('d/m/Y') : null;
    }

    /**
     *  Format a date string to a localized long form with weekday (e.g. Friday 21 March 2025).
     *
     * @param string $date Date in Y-m-d format.
     * @return string Formatted date or error message.
     */
    public static function long(string $date): ?string
    {
        $dt = self::parseFlexible($date);
        if (!$dt) return null;

        $formatter = new IntlDateFormatter(
            'fr_FR',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE
        );
        return ucfirst($formatter->format($dt));
    }

    /**
     * Format a date string to "month year" (e.g. March 2025).
     *
     * @param string $date Date in Y-m-d format.
     * @return string Formatted date or error message.
     */
    public static function monthYear(string $date): ?string
    {
        $dt = self::parseFlexible($date);
        if (!$dt) return null;

        $formatter = new IntlDateFormatter(
            'fr_FR',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            null,
            null,
            'MMMM yyyy'
        );
        return ucfirst($formatter->format($dt));
    }

    /**
     * Format a date string to "weekday dd/MM" (e.g. Monday 21/03).
     *
     * @param string $date Date in Y-m-d format.
     * @return string Formatted date or error message.
     */
    public static function weekday(string $date): ?string
    {
        $dt = self::parseFlexible($date);
        if (!$dt) return null;

        $formatter = new IntlDateFormatter(
            'fr_FR',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            'Europe/Paris',
            IntlDateFormatter::GREGORIAN,
            'EEEE dd/MM'
        );
        return ucfirst($formatter->format($dt));
    }

    /**
     * Format a time value (e.g. 12:00:00 → 12h00).
     *
     * @param string $time Time in H:i:s format.
     * @return string Formatted time or error message.
     */
    public static function time(?string $time): string
    {
        if (!$time) return '';

        $dt = DateTime::createFromFormat('H:i:s', $time) ?: DateTime::createFromFormat('H:i', $time);
        return $dt ? $dt->format('H\hi') : htmlspecialchars($time);
    }
}
