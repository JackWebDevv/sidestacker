<?php

function human_time_diff($timestamp) {
    $current_time = time();
    $diff = $current_time - $timestamp;
    
    $intervals = array(
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );
    
    foreach ($intervals as $seconds => $label) {
        $count = floor($diff / $seconds);
        if ($count > 0) {
            if ($count == 1) {
                return "1 $label";
            } else {
                return "$count {$label}s";
            }
        }
    }
    
    return "just now";
}
