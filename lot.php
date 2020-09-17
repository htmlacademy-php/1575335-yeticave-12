<?php

require './helpers.php';

session_start();
$is_auth = $_SESSION['user_logged_in'] ?? false;
$user_name = $_SESSION['user_name'] ?? "";
$title = 'Информация о лоте';
$categories = get_categories();
$lot_result = [];
$remaining_time = ['00', '00'];

$connection = mysqli_connect('localhost', 'root', 'root', 'yeti_cave_db');

$lot_id = filter_input(INPUT_GET, 'id');

if (is_null($lot_id)) {

    send_status_404_page('pages/404.html');

} else {

    $sql_lot = "SELECT lots.*, cat.name FROM lots
INNER JOIN categories as cat
ON lots.category_id = cat.category_id
WHERE lot_id = ?";

    mysqli_set_charset($connection, "utf8");
    $lot_prepared = db_get_prepare_stmt($connection, $sql_lot, [$lot_id]);
    mysqli_stmt_execute($lot_prepared);
    $result = mysqli_stmt_get_result($lot_prepared);

    if (mysqli_num_rows($result) != 0) {

        $lot_result = mysqli_fetch_assoc($result);
        $title = $lot_result['lot_name'] ?? "Информация о лоте";
        if (isset($lot_result['date_end'])) {

            $remaining_time = get_time_remaining($lot_result['date_end']);

        } else {

            $remaining_time = ['00', '00'];

        }
    } else {

        send_status_404_page('pages/404.html');

    }
}

$page_content = include_template('/lot_page.php',
    ['lot' => $lot_result, 'remaining_time' => $remaining_time, 'is_auth' => $is_auth]);
$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'title' => $title,
    'categories' => $categories,
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
