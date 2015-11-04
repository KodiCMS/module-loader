<?php

if (!function_exists('normalize_path')) {
    /**
     * @param string $path
     *
     * @return string
     */
    function normalize_path($path)
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }
}
