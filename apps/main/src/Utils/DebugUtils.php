<?php

namespace App\Utils;

class DebugUtils
{
    public static function getMemoryUsage(): string
    {
        $mem_usage = memory_get_usage(true);

        if ($mem_usage < 1024)
            return $mem_usage . " Bytes";
        elseif ($mem_usage < 1048576)
            return round($mem_usage / 1024, 2) . " KB";
        else
            return round($mem_usage / 1048576, 2) . " MB";
    }
}