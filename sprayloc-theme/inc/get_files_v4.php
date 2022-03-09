<?php
require_once("../../../../wp-load.php");

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


$full_data["equipment"] = array();
$full_data["files"] = array();
$full_data["folders"] = array();
$full_data["kits"] = array();


function make_all_requests()
{
    global $full_data;

    $ch_equipment = init_curl_request("https://api.rentman.net/equipment");
    $handles["equipment"] = $ch_equipment;
    
    $ch_files = init_curl_request("https://api.rentman.net/files");
    $handles["files"] = $ch_files;
    
    $ch_folders = init_curl_request("https://api.rentman.net/folders");
    $handles["folders"] = $ch_folders;
    
    $ch_kits = init_curl_request("https://api.rentman.net/equipmentsetscontent");
    $handles["kits"] = $ch_kits;
    
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
            // echo  "Key :".$key."<br>";
            // echo "items count : ".$data->itemCount ."<br>";
            // echo "Limit : ".$data->limit ."<br>";
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

/* filter archived files */

$full_data["equipment"] = array_values(array_filter($full_data["equipment"], function ($item) {
    return $item->in_archive == false;
}));


/* filter by old custom option visible_on_site */
$full_data["equipment"] = array_values(array_filter($full_data["equipment"], function ($item) {
    /**
     * custom->custom_1 : visible_on_site option in rentman app
     */
    
    return $item->custom->custom_1 == 1;
}));

// $full_data["equipment"] = array_values(array_filter($full_data["equipment"], function ($item) {
//     return $item->in_shop == true;
// }));



/* filter folders that are not equipment type */
$full_data["folders"] = array_values(array_filter($full_data["folders"], function ($item) {
    return $item->itemtype == 'equipment';
}));
/* filter files that are not image type */
$full_data["files"] = array_values(array_filter($full_data["files"], function ($item) {
    return $item->image != false;
}));
// var_dump($full_data["files"]);
echo json_encode($full_data);
