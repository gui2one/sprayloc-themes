<?php
require_once("../../../../wp-load.php");


$response = array();
$response["success"] = true;

$already_there = 0;
$inc_num_files = 0;

$full_data["files"] = array();
$gallery_folder = "gallery/";

if( !file_exists($gallery_folder)){
    mkdir($gallery_folder, 0777, true);
}
// $handles = array();
function init_curl_request($url)
{
    // global $handles;
    $ch = curl_init();

    curl_setopt_array($ch, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'Content-type: application/json',
        'Authorization: Bearer '.get_option('sprayloc_plugin_admin_option_name')["rentman_api_key"],
        'Content-length: 0',
    ),
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_SSL_VERIFYPEER => 1
    ));

    // array_push($handles, $ch);
    return $ch;
}


function make_all_requests()
{
    global $full_data;


    
    $ch_files = init_curl_request("https://api.rentman.net/files");
    $handles["files"] = $ch_files;

    
    $attempts = 0;
    $max_attempts = 15;
    while (count($handles) > 0 && $attempts < $max_attempts) {
    
        // echo "--> Attempts : ".$attempts."<br>";
    
        $mh = curl_multi_init();
        foreach ($handles as $key => $handle) {
            curl_multi_add_handle($mh, $handle);
        }
    
        
    
        $messages = -1;
        do {
            $status = curl_multi_exec($mh, $active);
            $infos = curl_multi_info_read($mh, $messages);
            if ($active) {
                curl_multi_select($mh);
            }
        } while ($active && $status == CURLM_OK);
    
        $handles_to_remove = [];
        foreach ($handles as $key => $handle) {
            $data = json_decode(curl_multi_getcontent($handle));
            // var_dump($data);
            $data->data = array_filter($data->data, function ($value) {
                if (key_exists("in_archive", $value)) {
                    return $value->in_archive == false;
                } else {
                    return true;
                }

                return false;
            });
            $full_data[$key] = array_merge($full_data[$key], $data->data);

            if ($data->itemCount < $data->limit) {
    
                /// got everything , remove handle"
                $handles_to_remove[] = $handle;
                curl_multi_remove_handle($mh, $handle);
            }
        }
    
        //remove finished handles from $handles array
        foreach ($handles_to_remove as $to_remove) {
            $index = array_search($to_remove, $handles);
    
            if ($index != false) {
                // remove handle from array
                unset($handles[$index]);
            }
        }
    
        // redo needed requests
    
        if (count($handles)> 0) {
    
            // echo "remake needed requests ...<br>";
            foreach ($handles as $key => $handle) {
                switch ($key) {
                    case "equipment":
                        curl_setopt($handle, CURLOPT_URL, "https://api.rentman.net/equipment?offset=300");
                        break;
                    case "files":
                        curl_setopt($handle, CURLOPT_URL, "https://api.rentman.net/files?offset=300");
                        break;
                    case "folders":
                        curl_setopt($handle, CURLOPT_URL, "https://api.rentman.net/folders?offset=300");
                        break;
                    case "kits":
                        curl_setopt($handle, CURLOPT_URL, "https://api.rentman.net/equipmentsetscontent?offset=300");
                        break;
                    default:
                        break;
                }
            }
        }
    
    
        $attempts++;
    }
}

make_all_requests();

/* filter files that are not image type */
$full_data["files"] = array_values(array_filter($full_data["files"], function ($item) {
    return $item->image != false;
}));

// phpinfo();
// var_dump($full_data["files"]);




function imagethumb($image_src, $max_size = 250, $displayname = null, $expand = false, $square = false)
{
    global $gallery_folder;
    global $already_there;
    // echo "Display name : ".$displayname."<br>";
    // echo "Img Src : ".$image_src."<br>";
    $image_src_path = $image_src;
    $info = pathinfo($image_src);
    if ($displayname) {
        $baseName = basename($displayname, ".".$info['extension']);
    } else {
        $baseName = basename($image_src, ".".$info['extension']);
    }
    // echo $baseName."<br>";
    $image_dest = "".$baseName."_thumbnail.".$info['extension']."";
    
    $srcFileName = $baseName.".".$info["extension"];
    $fullScrPath = $gallery_folder."".$srcFileName;
    // // echo $fullScrPath."<br>";
    // if (!file_exists($fullScrPath)) {
    //     echo "File not found --> ".$fullScrPath."<br> ";
    //     // echo "Creating it ...<br>";

    //     file_put_contents($fullScrPath, file_get_contents($image_src));
    //     // return FALSE;
    // }

    if (file_exists($gallery_folder."".$image_dest)) {
        // echo "thumbnail OK ....<br>";
        $already_there += 1;
        return false;
    }

    // Récupère les infos de l'image
    $fileinfo = getimagesize($image_src);

    
    if (!$fileinfo) {
        return false;
    }

    $width     = $fileinfo[0];
    $height    = $fileinfo[1];
    $type_mime = $fileinfo['mime'];
    $type      = str_replace('image/', '', $type_mime);

    if (!$expand && max($width, $height)<=$max_size && (!$square || ($square && $width==$height))) {
        // L'image est plus petite que max_size
        if ($image_dest) {
            return copy($image_src, $gallery_folder."".$image_dest);
        } else {
            header('Content-Type: '. $type_mime);
            return (boolean) readfile($image_src);
        }
    }

    // Calcule les nouvelles dimensions
    $ratio = $width / $height;

    if ($square) {
        $new_width = $new_height = $max_size;

        if ($ratio > 1) {
            // Paysage
            $src_y = 0;
            $src_x = round(($width - $height) / 2);

            $src_w = $src_h = $height;
        } else {
            // Portrait
            $src_x = 0;
            $src_y = round(($height - $width) / 2);

            $src_w = $src_h = $width;
        }
    } else {
        $src_x = $src_y = 0;
        $src_w = $width;
        $src_h = $height;

        if ($ratio > 1) {
            // Paysage
            $new_width  = $max_size;
            $new_height = round($max_size / $ratio);
        } else {
            // Portrait
            $new_height = $max_size;
            $new_width  = round($max_size * $ratio);
        }
    }

    // Ouvre l'image originale
    $func = 'imagecreatefrom' . $type;
    if (!function_exists($func)) {
        return false;
    }

    $image_src = $func($image_src);
    $new_image = imagecreatetruecolor($new_width, $new_height);

    // Gestion de la transparence pour les png
    if ($type=='png') {
        imagealphablending($new_image, false);
        if (function_exists('imagesavealpha')) {
            imagesavealpha($new_image, true);
        }
    }

    // Gestion de la transparence pour les gif
    elseif ($type=='gif' && imagecolortransparent($image_src)>=0) {
        $transparent_index = imagecolortransparent($image_src);
        $transparent_color = imagecolorsforindex($image_src, $transparent_index);
        $transparent_index = imagecolorallocate($new_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
        imagefill($new_image, 0, 0, $transparent_index);
        imagecolortransparent($new_image, $transparent_index);
    }

    // Redimensionnement de l'image
    imagecopyresampled(
        $new_image,
        $image_src,
        0,
        0,
        $src_x,
        $src_y,
        $new_width,
        $new_height,
        $src_w,
        $src_h
    );

    // Enregistrement de l'image
    $func = 'image'. $type;
    if ($image_dest) {
        $func($new_image, $gallery_folder."".$image_dest);
    } else {
        header('Content-Type: '. $type_mime);
        $func($new_image);
    }



    // Libération de la mémoire
    imagedestroy($new_image);
    return true;
}



foreach ($full_data["files"] as $value) {
    $image_src = $value->url;
    $displayname = $value->displayname;

    $infos = pathinfo($displayname);
    $sanitized = preg_replace('/[^a-zA-Z0-9.]/', '_', $infos["filename"]);
    
    $sanitized = str_replace(".", "_", $sanitized);
    // remove double _ ie :'__' that created when replacing accented chars, this apparently does not happen in javascript ...
    // to be aware of ...
    // $sanitized = str_replace("__", "_", $sanitized);
    $sanitized = preg_replace("/(_+)/", "_", $sanitized);
    
    imagethumb($image_src, 250, $sanitized);
    
    // echo $sanitized."<br>";
    $inc_num_files += 1;
}

$response["num_files"] = $inc_num_files;
$response["already_there"] = $already_there;
$json = json_encode($response);
print_r($json);
