<?php
require './helpers.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $connection = mysqli_connect('localhost','root','root','yeti_cave_db');
    if (!$connection)
    {
        print('Ошибка подключения к БД: ' . mysqli_connect_error());
    } else {
        $required_fields = ['email', 'password', 'name', 'message'];
        $rules = [
            'email' => function() {
                return validate_email('email');
            },
            'password' => function() {
                return validate_password('password');
            },
            'name' => function() {
                return validate_username('name');
            },
            'message' => function() {
                return validate_filled('message');
            }
            ];
    
        foreach ($_POST as $key => $value) {
            if (isset($rules[$key])) {
                $rule = $rules[$key];
                $errors[$key] = $rule();
            }
        }
        $errors = array_filter($errors);
    
        if(empty($errors)){
            
            $sql_new_user = "INSERT INTO users (email, user_name, password, contacts) VALUES (?, ?, ?, ?)";
            $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $prepared_sql = db_get_prepare_stmt($connection, $sql_new_user, [strtolower($_POST['email']), $_POST['name'], $password_hash, $_POST['message']]);
            
            if (mysqli_stmt_execute($prepared_sql)){

            header("Location: /pages/login.html"); 
            die(); 

            } else {
            
                print 'Ошибка запроса на сохранение данных ' . mysqli_error($connection);
            
            }
            
        }
    }
}


$page_content = include_template('/sign-up.php', ['errors'=>$errors]);
print ($page_content);
