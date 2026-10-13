<?php

if (!function_exists('censorLastName')) {
    /**
     * Censor the last name(s) of a full name
     * Example: "Juan Dela Cruz" becomes "Juan D***"
     * Example: "Maria Santos Garcia" becomes "Maria S***** G*****"
     *
     * @param string|null $fullName
     * @return string
     */
    function censorLastName(?string $fullName): string
    {
        if (empty($fullName)) {
            return 'N/A';
        }

        return trim($fullName);
    }
}

if (!function_exists('censorName')) {
    /**
     * Alias for censorLastName
     *
     * @param string|null $fullName
     * @return string
     */
    function censorName(?string $fullName): string
    {
        return censorLastName($fullName);
    }
}
