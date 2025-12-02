<?php

namespace app\helpers;

class HelperTabulator
{
    public static function pagination($query, $pageNo, $size)
    {
        // set limit offset
        $query->limit($size);

        $offset = 0;

        if ($pageNo > 1) {
            $offset = ($pageNo * $size) - $size;
        }

        return $query->offset($offset);
    }

    public static function resultFormat($all_count, $data, $size)
    {
        return ['last_page' => ceil($all_count / $size), 'data' => $data];
    }
}
