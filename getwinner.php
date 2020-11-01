<?php

require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$connection = mysqli_connect('localhost', 'root', 'root', 'yeti_cave_db');

if (!$connection) {
    print('Ошибка подключения к БД: ' . mysqli_connect_error());
} else {
    mysqli_set_charset($connection, "utf8");

    $sql_find_winners = "SELECT a.bid_id, a.lot_id, a.user_id, a.date_time, a.bid_price, users.user_name, users.email, lots.lot_name
FROM bids a
INNER JOIN (
	SELECT lot_id, MAX(date_time) AS date_time
	FROM bids
	GROUP BY lot_id) AS b
ON a.lot_id = b.lot_id AND a.date_time = b.date_time
LEFT JOIN lots
ON lots.lot_id = a.lot_id
LEFT JOIN users
ON a.user_id = users.user_id
WHERE winner IS NULL AND lots.date_end <= CURDATE()
ORDER BY bid_id";

    $winners_info = mysqli_fetch_all(mysqli_query($connection, $sql_find_winners), MYSQLI_ASSOC);

    if (!empty($winners_info) && isset($_ENV['MAILTRAP_LOGIN']) && isset($_ENV['MAILTRAP_PASSWORD'])) {
        $transport = (new Swift_SmtpTransport('smtp.mailtrap.io', 465, 'tls'))
            ->setUsername($_ENV['MAILTRAP_LOGIN'])
            ->setPassword($_ENV['MAILTRAP_PASSWORD']);
        $mailer = new Swift_Mailer($transport);
        $message = (new Swift_Message('Ваша ставка победила'))
            ->setFrom(['keks@phpdemo.ru' => 'Keks'])
            ->setContentType('text/html');

        foreach ($winners_info as $winner) {
            if (!isset($winner['user_name'], $_SERVER['SERVER_NAME'], $winner['lot_id'], $winner['lot_name'], $winner['email'], $winner['user_id'], $winner['lot_id'])) {
                continue;
            }
            $sql_update_winners = "UPDATE lots
SET winner = ${winner['user_id']}
WHERE lot_id = ${winner['lot_id']}";
            $update_winner = mysqli_query($connection, $sql_update_winners);

            if ($update_winner) {
                $mail_content = include_template('/email.php', [
                    'user_name' => $winner['user_name'],
                    'host' => $_SERVER['SERVER_NAME'],
                    'lot_id' => $winner['lot_id'],
                    'lot_name' => $winner['lot_name'],
                ]);
                $message->setTo([$winner['email'] => $winner['user_name']])
                    ->setBody($mail_content);
                $result = $mailer->send($message);
            }
        }
    }
}
