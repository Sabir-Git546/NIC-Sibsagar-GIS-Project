<?php
// helper function for creating sanitizer for protection against XSS
if (!function_exists('sanitize_input')) {

    function sanitize_input($input)
    {
        if (is_array($input)) {
            return array_map('sanitize_input', $input);
        }

        $input = trim($input);

        // Remove HTML + JS
        $input = strip_tags($input);

        // Convert special chars
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');

        return $input;
    }
}