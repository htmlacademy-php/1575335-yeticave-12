<?php
require './helpers.php';

$is_auth = rand(0, 1);

$user_name = 'Dinar'; // укажите здесь ваше имя

$connection = mysqli_connect('localhost','root','root','yeti_cave_db');

$items = [];
$categories = get_categories();

if (!$connection) {
    
    print('Ошибка подключения к БД: ' . mysqli_connect_error());
    
} else {

    mysqli_set_charset($connection, "utf8");
    $sql_items = "SELECT lots.lot_id, lot_name AS 'name', cat.name AS 'category', img_url, starting_price AS 'price',  MAX(bid_price) AS 'bid_price' , date_end AS 'expiration_date' 
        FROM lots 
        INNER JOIN categories AS cat ON lots.category_id = cat.category_id
        LEFT JOIN bids ON lots.lot_id = bids.lot_id
        WHERE date_end > CURDATE()
        GROUP BY lots.lot_id
        ORDER BY date_created DESC";
    $items_res = mysqli_query($connection, $sql_items);
    
    if ($items_res) { 
    
        $items = mysqli_fetch_all($items_res, MYSQLI_ASSOC);
        
    } else {
    
        print('Ошибка запроса: ' . mysqli_error($connection));
    
    }
    
}


$page_content = include_template('/main.php', ['categories'=>$categories, 'items'=>$items ]);

$layout_content = include_template('/layout.php', ['content' =>$page_content, 'title' => 'Главная' ,'categories' => $categories, 'is_auth' => $is_auth, 'user_name' => $user_name, 'homepage' => true ]); 

print($layout_content);
