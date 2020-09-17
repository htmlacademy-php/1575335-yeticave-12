<?php
require './helpers.php';

session_start();
$is_auth = $_SESSION['user_logged_in'] ?? false;
$user_name = $_SESSION['user_name'] ?? "";
$categories = get_categories();
$items = [];
$pages = [];
$search = "";
$num_pages = 0;
$connection = mysqli_connect('localhost', 'root', 'root', 'yeti_cave_db');

if (!$connection) {
    print('Ошибка подключения к БД: ' . mysqli_connect_error());
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $limit = 9;
    $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT) ?? 1;
    $offset = ($page - 1) * $limit;
    $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING) ?? "";
    $search = trim($search);

    if (strlen($search) > 0) {

        $sql_search = "SELECT SQL_CALC_FOUND_ROWS lot_id, date_created, lot_name, img_url, starting_price, date_end, cats.name AS category
        FROM lots
        LEFT JOIN categories AS cats
        ON lots.category_id = cats.category_id
        WHERE MATCH(lot_name, lot_description)
        AGAINST(?)
        ORDER BY date_created DESC
        LIMIT ?
        OFFSET ?";

        $sql_num_items = "SELECT FOUND_ROWS()";
        $search_result = db_get_prepare_stmt($connection, $sql_search, [$search, $limit, $offset]);

        if (!mysqli_stmt_execute($search_result)) {
            print('Ошибка запроса ' . mysqli_error($connection));
        }

        $result = mysqli_stmt_get_result($search_result);
        $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $num_items = mysqli_fetch_assoc(mysqli_query($connection, $sql_num_items));
        $num_pages = ceil($num_items['FOUND_ROWS()'] / $limit);

        switch ($page) {
            case $num_pages:
                $pages = [$page - 1, $page, null];
                break;
            case 1:
                $pages = [null, $page, $page + 1];
                break;
            default:
                $pages = [$page - 1, $page, $page + 1];
                break;
        }
        if ($num_pages <= 1) {
            $pages = [];
        }
    }
}


$page_content = include_template('/search_page.php', [
    'categories' => $categories,
    'items' => $items,
    'search_query' => $search,
    'num_pages' => $num_pages,
    'pages' => $pages
]);

$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'title' => 'Результаты поиска',
    'categories' => $categories,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'search_query' => $search
]);

print($layout_content);
