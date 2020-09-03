<?php

require './helpers.php';

$connection = mysqli_connect('localhost', 'root', 'root', 'yeti_cave_db');

$lot_id = filter_input(INPUT_GET, 'id');

$lot_result = [];  

if (is_null($lot_id)) {
    
   send_status_404_page('pages/404.html');
    
} else {
    
    $sql_lot = "SELECT lots.*, cat.name FROM lots
INNER JOIN categories as cat
ON lots.category_id = cat.category_id
WHERE lot_id = ?";

    
    $lot_prepared = db_get_prepare_stmt($connection, $sql_lot, [$lot_id]);
    mysqli_stmt_execute($lot_prepared);
    $result = mysqli_stmt_get_result($lot_prepared);
    
    if (mysqli_num_rows($result)!=0){
        
        $lot_result = mysqli_fetch_assoc($result);
        
        if (isset($lot_result['date_end'])){
            
        $remaining_time = get_time_remaining($lot_result['date_end']);
        
        } else {
            
            $remaining_time = ['00', '00'];
            
        }
    } else {
        
        send_status_404_page('pages/404.html');
        
    }
}

$page_content = include_template('/lot_page.php', ['lot'=>$lot_result, 'remaining_time' => $remaining_time]);

print($page_content);
