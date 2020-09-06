<?php
require './helpers.php';

$connection = mysqli_connect('localhost','root','root','yeti_cave_db');
$categories = [];
$errors = [];
if (!$connection) {
    
    print('Ошибка подключения к БД: ' . mysqli_connect_error());
    
} else {
    
    mysqli_set_charset($connection, "utf8");
    $sql_categories = "SELECT category_id, name, symbol_code  FROM categories";
    $res_categories = mysqli_query($connection, $sql_categories);
    if ($res_categories) { 
    
        $categories = mysqli_fetch_all($res_categories, MYSQLI_ASSOC);
    
    } else {
        
        print('Ошибка запроса: ' . mysqli_error($connection));
        
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $required_fields = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date' ];
    $rules = [
        'lot-name' => function() {
            return validate_filled('lot-name');
        },
        'category' => function() {
            return validate_category('category');
        },
        'message' => function() {
            return validate_filled('message');
        },
        'lot-rate' => function() {
            return validate_starting_price('lot-rate');
        },
        'lot-step' => function() {
            return validate_step('lot-step');
        },
        'lot-date' => function() {
            return validate_date_end('lot-date');
        }
        ];
    
    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }
    
    $errors['lot-img'] = validate_image('lot-img', ['image/png', 'image/jpeg']); 
    
    $errors = array_filter($errors);
    
    if(count($errors)== 0 && $connection && isset($categories) && $file_url = save_image('lot-img')){
        
        $sql_add_lot = "INSERT INTO lots (lot_name, lot_description, img_url, date_end, starting_price, rate, author_id, category_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $prepared_sql = db_get_prepare_stmt($connection, $sql_add_lot, [$_POST['lot-name'], $_POST['message'], $file_url, $_POST['lot-date'], $_POST['lot-rate']*100, $_POST['lot-step']*100, 2, $_POST['category']]);
        
        if (mysqli_stmt_execute($prepared_sql)){
        $result = mysqli_stmt_get_result($prepared_sql);
        $last_id = mysqli_insert_id($connection);
        header("Location: /lot.php?id=$last_id"); 
        die(); 
        } else {
            
            print 'Ошибка запроса на сохранение данных ' . mysqli_error($connection);
            
        }
    }
}
    

$page_content = include_template('/add-lot.php', ['categories' => $categories, 'errors' => $errors]);
print($page_content);
