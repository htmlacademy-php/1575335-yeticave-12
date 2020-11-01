<?php

require './helpers.php';

session_start();
$is_auth = $_SESSION['user_logged_in'] ?? false;
$user_name = $_SESSION['user_name'] ?? "";
$user_id = $_SESSION['user_id'] ?? -1;
$categories = get_categories();
$mybids = [];
if (!$is_auth) {
    header("Location: /login.php");
    die();
}
$connection = mysqli_connect('localhost', 'root', 'root', 'yeti_cave_db');

if (!$connection) {
    print('Ошибка подключения к БД: ' . mysqli_connect_error());
}

mysqli_set_charset($connection, "utf8");
$sql_mybids = "SELECT  lots.lot_id, lots.lot_name, lots.date_end, lots.img_url, lots.winner,  users.contacts, categories.name AS category, MAX(bids.bid_price) AS bid_price, MAX(bids.date_time) AS date_time
FROM bids
LEFT JOIN lots ON bids.lot_id = lots.lot_id
LEFT JOIN users ON lots.author_id = users.user_id
LEFT JOIN categories ON lots.category_id = categories.category_id
WHERE bids.user_id = ${user_id}
GROUP BY bids.lot_id
ORDER BY date_time DESC";

$sql_result = mysqli_query($connection, $sql_mybids);

if ($sql_result) {
    $mybids = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);
    foreach ($mybids as &$bid) {
        if (isset ($bid['winner']) && (int)$bid['winner'] === $user_id) {
            $bid['is_winner'] = true;
        }
        if (isset($bid['date_end'])) {
            $remaining_time = get_time_remaining($bid['date_end']);
            $bid['remaining_time'] = $remaining_time;

            if (isset($remaining_time[0], $remaining_time[1]) && $remaining_time[0] === '00' && $remaining_time[1] === '00') {
                $bid['lot_closed'] = true;
            }
        }
    }
    unset($bid);
} else {
    print('Ошибка запроса ' . mysqli_error($connection));
}
$page_content = include_template('/my_bids_page.php', ['mybids' => $mybids]);
$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'title' => 'Мои ставки',
    'categories' => $categories,
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
