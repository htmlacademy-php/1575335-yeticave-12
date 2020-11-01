<?php

require './helpers.php';
session_start();
if (!isset($_SESSION['user_logged_in'])) {
    http_response_code(403);
    die();
}

$is_auth = $_SESSION['user_logged_in'];
$user_name = $_SESSION['user_name'] ?? "";
$user_id = $_SESSION['user_id'] ?? -1;
$connection = mysqli_connect('localhost', 'root', 'root', 'yeti_cave_db');
$categories = get_categories();
$errors = [];
if (!$connection) {
    print('Ошибка подключения к БД: ' . mysqli_connect_error());
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required_fields = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];
    $rules = [
        'lot-name' => function () {
            return required_field_validation_errors('lot-name');
        },
        'category' => function () {
            return lot_category_validation_errors('category');
        },
        'message' => function () {
            return required_field_validation_errors('message');
        },
        'lot-rate' => function () {
            return starting_price_validation_errors('lot-rate');
        },
        'lot-step' => function () {
            return step_validation_errors('lot-step');
        },
        'lot-date' => function () {
            return date_end_validation_errors('lot-date');
        }
    ];

    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }

    $errors['lot-img'] = image_validation_errors('lot-img', ['image/png', 'image/jpeg']);
    $errors = array_filter($errors);

    if ($connection && isset($categories) && count($errors) === 0 && $file_url = save_image('lot-img')) {
        mysqli_set_charset($connection, "utf8");

        $sql_add_lot = "INSERT INTO lots (lot_name, lot_description, img_url, date_end, starting_price, rate, author_id, category_id)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $prepared_sql = db_get_prepare_stmt($connection, $sql_add_lot, [
            $_POST['lot-name'],
            $_POST['message'],
            $file_url,
            $_POST['lot-date'],
            $_POST['lot-rate'] * 100,
            $_POST['lot-step'] * 100,
            $user_id,
            $_POST['category']
        ]);

        if (mysqli_stmt_execute($prepared_sql)) {
            $result = mysqli_stmt_get_result($prepared_sql);
            $last_id = mysqli_insert_id($connection);
            header("Location: /lot.php?id=$last_id");
            die();
        }
        print 'Ошибка запроса на сохранение данных ' . mysqli_error($connection);
    }
}


$page_content = include_template('/add-lot.php', ['categories' => $categories, 'errors' => $errors]);
$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'title' => 'Добавление лота',
    'categories' => $categories,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'third_css' => true
]);
print($layout_content);
