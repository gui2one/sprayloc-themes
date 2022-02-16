<?php

$directory = "gallery";
foreach (glob($directory.'/*.*') as $file) {
    $info = pathinfo($file);
    // print_r($info);
    $name = $info["filename"];
    $is_not_thumbnail = strpos($name, "_thumbnail") === false;

    if ($is_not_thumbnail) {
        // print_r($name);
        // print_r("<br>");
        unlink($file);
    } else {
        // print_r($name);
        // print_r("<br>");
    }
}
