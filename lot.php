<?php

require './helpers.php';

session_start();
$is_auth = $_SESSION['user_logged_in'] ?? false;
$user_name = $_SESSION['user_name'] ?? "";
$categories = get_categories();
$lot_result = [];
$errors = [];
$all_bids = [];
$show_bids = true;
$remaining_time = ['00', '00'];
$title = 'Информация о лоте';
$lot_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (is_null($lot_id)) {

    send_status_404_page('pages/404.html');

}

$connection = mysqli_connect('localhost', 'root', 'root', 'yeti_cave_db');
if (!$connection) {
    print('Ошибка подключения к БД: ' . mysqli_connect_error());
}
mysqli_set_charset($connection, "utf8");

$sql_lot = "SELECT lots.lot_id, lot_name, cat.name AS 'category', img_url, lot_description, starting_price, MAX(bid_price) as 'current_price', rate, date_end, author_id, winner
FROM lots
INNER JOIN categories as cat ON lots.category_id = cat.category_id
LEFT JOIN bids ON lots.lot_id = bids.lot_id
WHERE lots.lot_id = ?
GROUP BY lots.lot_id";


$lot_prepared = db_get_prepare_stmt($connection, $sql_lot, [$lot_id]);
mysqli_stmt_execute($lot_prepared);
$result = mysqli_stmt_get_result($lot_prepared);

if (mysqli_num_rows($result) !== 0) {

    $lot_result = mysqli_fetch_assoc($result);
    $title = $lot_result['lot_name'] ?? "Информация о лоте";
    $lot_result['current_price'] = $lot_result['current_price'] ?? $lot_result['starting_price'];
    $lot_result['min_bid'] = $lot_result['current_price'] + $lot_result['rate'];
    if (isset($lot_result['date_end'])) {

        $remaining_time = get_time_remaining($lot_result['date_end']);

    } else {

        $remaining_time = ['00', '00'];

    }
    if ($remaining_time[0] === '00' && $remaining_time[1] === '00') {
        $lot_result['lot_closed'] = true;
    }
} else {

    send_status_404_page('pages/404.html');

}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $min_bid = $lot_result['min_bid'];
    $rules = [
        'cost' => function () use (&$min_bid) {
            return validate_bid('cost', $min_bid);
        }
    ];

    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }
    $errors = array_filter($errors);

    if (empty($errors) && isset($_SESSION['user_logged_in']) && time() < strtotime($lot_result['date_end'])) {

        $bid = filter_input(INPUT_POST, 'cost', FILTER_SANITIZE_NUMBER_INT);
        $sql_bid = "INSERT INTO bids (bid_price, user_id, lot_id)
        VALUES (?, ?, ?)";
        $bid_prepared = db_get_prepare_stmt($connection, $sql_bid, [$bid * 100, $_SESSION['user_id'], $lot_id]);

        if (!mysqli_stmt_execute($bid_prepared)) {
            $errors['cost'] = 'Ошибка добавления ставки ' . mysqli_error($connection);
        } else {
            $lot_result['current_price'] = $bid * 100;
            $lot_result['min_bid'] = $bid * 100 + $lot_result['rate'];
        }
    }
}

$sql_all_bids = "SELECT users.user_id, user_name, bids.bid_price AS bid_price, bids.date_time FROM bids
INNER JOIN users ON bids.user_id = users.user_id
WHERE lot_id = ?
ORDER BY date_time DESC
LIMIT 10";
$all_bid_prepared = db_get_prepare_stmt($connection, $sql_all_bids, [$lot_id]);
mysqli_stmt_execute($all_bid_prepared);
$all_bid_result = mysqli_stmt_get_result($all_bid_prepared);

if (mysqli_num_rows($all_bid_result) != 0) {
    $all_bids = mysqli_fetch_all($all_bid_result, MYSQLI_ASSOC);
}

if (!isset($_SESSION['user_logged_in']) || strtotime($lot_result['date_end']) < time() || $lot_result['author_id'] === $_SESSION['user_id'] || isset($all_bids[0]['user_id']) && $all_bids[0]['user_id'] === $_SESSION['user_id']) {
    $show_bids = false;
}


$page_content = include_template('/lot_page.php', [
    'lot' => $lot_result,
    'remaining_time' => $remaining_time,
    'show_bids' => $show_bids,
    'errors' => $errors,
    'bids' => $all_bids
]);
$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'title' => $title,
    'categories' => $categories,
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
