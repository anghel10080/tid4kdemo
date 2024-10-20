<?php
function sanitizeNullValues($data) {
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $data[$key] = sanitizeNullValues($value);  // reapelare pentru sub-array
        } else {
            if (is_null($value)) {
                $data[$key] = "";  // înlocuirea valorii NULL cu un șir gol
            }
        }
    }
    return $data;
}

function sanitizeNullValuesInString($string) {
    $parts = explode(' ', $string);
    foreach ($parts as $key => $part) {
        if (is_null($part)) {
            $parts[$key] = "";
        }
    }
    return implode(' ', $parts);
}
