<?php
if (!function_exists('is_development')) {
    function is_development() {
        return app()->environment('local') || app()->environment('development');
    }
}

if (!function_exists('get_exception_message')) {
    function get_exception_message(string $message, string $exceptionMessage): string
    {
        return is_development() ? $message . ' Error:' . $exceptionMessage : $message;
    }
}
