<?php
require './helpers.php';
session_start();
$is_auth = $_SESSION['user_logged_in'] ?? false;
$user_name = $_SESSION['user_name'] ?? "";

$categories = get_categories();
$category_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$category_name = "нет категории";
$category_index = array_search($category_id, array_column($categories, 'category_id'));
if ($category_index !== false && isset($categories[$category_index]['name'])) {
    $category_name = htmlspecialchars($categories[$category_index]['name']);
} else {
    send_status_404_page('pages/404.html');
}

$title = 'Все лоты в категории ' . $category_name;
$pages = [];
$num_pages = 0;
$items_result = [];

$connection = mysqli_connect('localhost', 'root', 'root', 'yeti_cave_db');

if (!$connection) {
    print('Ошибка подключения к БД: ' . mysqli_connect_error());
} else {
    $limit = 9;
    $page = (int)filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
    if(!$page || !isset($page))
    {
        $page =1;
    }
    $offset = ($page - 1) * $limit;

    mysqli_set_charset($connection, "utf8");

    $sql_items = "SELECT SQL_CALC_FOUND_ROWS lots.lot_id, lots.lot_name, lots.img_url, lots.starting_price AS price, lots.date_end, cat.name AS 'category', bid_group.bid_count, bid_group.bid_price
FROM lots
INNER JOIN categories AS cat
ON lots.category_id = cat.category_id
LEFT JOIN (SELECT COUNT(bids.bid_id) AS bid_count, bids.lot_id, MAX(bids.bid_price) as bid_price
FROM bids
GROUP BY bids.lot_id) AS bid_group
ON lots.lot_id = bid_group.lot_id
WHERE date_end > CURDATE() AND cat.category_id = ?
ORDER BY lots.date_created DESC
LIMIT ?
OFFSET ?";
    $sql_num_items = "SELECT FOUND_ROWS()";

    $items_prepared = db_get_prepare_stmt($connection, $sql_items, [$category_id, $limit, $offset]);
    mysqli_stmt_execute($items_prepared);
    $result = mysqli_stmt_get_result($items_prepared);
    $num_items = mysqli_fetch_assoc(mysqli_query($connection, $sql_num_items));
    if(isset($num_items['FOUND_ROWS()'])) {
        $num_pages = ceil($num_items['FOUND_ROWS()'] / $limit);
    }
    if (mysqli_num_rows($result) !== 0) {
        $items_result = mysqli_fetch_all($result, MYSQLI_ASSOC);
        foreach ($items_result as &$item) {
            if (isset($item['date_end'])) {
                $remaining_time = get_time_remaining($item['date_end']);
                $item['remaining_time'] = $remaining_time;
            } else {
                $item['remaining_time'] = ['00', '00'];
            }
        }
        unset($item);
    }
    $pages = get_nav_pages($page, $num_pages);
}

$page_content = include_template('/category_page.php', [
    'categories' => $categories,
    'items' => $items_result,
    'category_name' => $category_name,
    'category_id' => $category_id,
    'pages' => $pages,
    'num_pages' => $num_pages
]);

$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'title' => $title,
    'categories' => $categories,
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
