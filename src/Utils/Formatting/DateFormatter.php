<?php

namespace App\Utils\Formatting;

use DateTime;
use IntlDateFormatter;

final class DateFormatter
{
    /**
     * Format a date to short format (e.g. 21/03/2025).
     *
     * @param string $date Date in Y-m-d format.
     * @return string Formatted date or error message.
     */
    public static function short(string $date): string
    {
        $dt = DateTime::createFromFormat('Y-m-d', $date);
        return $dt ? $dt->format('d/m/Y') : 'Invalid date format';
    }

    /**
     * Format a date to a long format with weekday (e.g. Friday 21 March 2025).
     *
     * @param string $date Date in Y-m-d format.
     * @return string Formatted date or error message.
     */
    public static function long(string $date): string
    {
        $dt = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dt) return 'Invalid date format';

        $formatter = new IntlDateFormatter(
            'fr_FR',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE
        );
        return ucfirst($formatter->format($dt));
    }

    /**
     * Format a date to month and year (e.g. March 2025).
     *
     * @param string $date Date in Y-m-d format.
     * @return string Formatted date or error message.
     */
    public static function monthYear(string $date): string
    {
        $dt = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dt) return 'Invalid date format';

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
     * Format a date to weekday and short date (e.g. Monday 21/03).
     *
     * @param string $date Date in Y-m-d format.
     * @return string Formatted date or error message.
     */
    public static function weekday(string $date): string
    {
        $dt = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dt) return 'Invalid date format';

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
    public static function time(string $time): string
    {
        $dt = DateTime::createFromFormat('H:i:s', $time);
        return $dt ? $dt->format('H\hi') : 'Invalid time format';
    }
}
