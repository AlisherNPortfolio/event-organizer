<?php
if (!function_exists('is_development')) {
    function is_development() {
        return app()->environment('local') || app()->environment('development');
    }
}
