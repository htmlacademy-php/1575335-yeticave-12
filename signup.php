<?php

require './helpers.php';
session_start();
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']) {
    http_response_code(403);
    die();
}
$errors = [];
$categories = get_categories();
$connection = mysqli_connect('localhost', 'root', 'root', 'yeti_cave_db');
if (!$connection) {
    print('Ошибка подключения к БД: ' . mysqli_connect_error());
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required_fields = ['email', 'password', 'name', 'message'];
    $rules = [
        'email' => function () {
            return email_validation_errors('email');
        },
        'password' => function () {
            return password_validation_errors('password');
        },
        'name' => function () {
            return username_validation_errors('name');
        },
        'message' => function () {
            return required_field_validation_errors('message');
        }
    ];

    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }
    $errors = array_filter($errors);

    if (empty($errors)) {
        mysqli_set_charset($connection, "utf8");
        $sql_new_user = "INSERT INTO users (email, user_name, password, contacts) VALUES (?, ?, ?, ?)";
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $prepared_sql = db_get_prepare_stmt($connection, $sql_new_user,
            [strtolower($_POST['email']), $_POST['name'], $password_hash, $_POST['message']]);

        if (mysqli_stmt_execute($prepared_sql)) {
            header("Location: /login.php");
            die();
        }
        print 'Ошибка запроса на сохранение данных ' . mysqli_error($connection);
    }
}


$page_content = include_template('/sign-up.php', ['errors' => $errors]);
$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'title' => 'Регистрация',
    'categories' => $categories,
    'is_auth' => false,
    'user_name' => ""
]);
print($layout_content);
