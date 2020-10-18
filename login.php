<?php
require './helpers.php';
$errors = [];
session_start();
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']) {
    header("Location: /index.php");
    die();
}

$categories = get_categories();
$connection = mysqli_connect('localhost', 'root', 'root', 'yeti_cave_db');
if (!$connection) {
    print('Ошибка подключения к БД: ' . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required_fields = ['email', 'password', 'name', 'message'];
    $rules = [
        'email' => function () {
            return validate_email_login('email');
        },
        'password' => function () {
            return validate_filled('password');
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
        $sql_authenticate = "SELECT user_id, user_name, email, password FROM users
        WHERE email = ? LIMIT 1";
        $prepared_sql = db_get_prepare_stmt($connection, $sql_authenticate, [strtolower($_POST['email'])]);
        mysqli_stmt_execute($prepared_sql);
        $prepared_res = mysqli_stmt_get_result($prepared_sql);
        $result = mysqli_fetch_all($prepared_res, MYSQLI_ASSOC);
        if (empty($result)) {
            $errors['password'] = 'Вы ввели неверный email/пароль';
        } elseif (!password_verify($_POST['password'], $result[0]['password'])) {
            $errors['password'] = 'Вы ввели неверный email/пароль';
        } else {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_name'] = $result[0]['user_name'];
            $_SESSION['user_id'] = $result[0]['user_id'];
            header("Location: /index.php");
            die();
        }
    }
}

$page_content = include_template('/login_page.php', ['errors' => $errors]);
$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'title' => 'Вход',
    'categories' => $categories,
    'is_auth' => false,
    'user_name' => ""
]);

print($layout_content);
