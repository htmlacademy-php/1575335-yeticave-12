<h1>Поздравляем с победой</h1>
<p>Здравствуйте, <?= strip_tags($user_name) ?></p>
<p>Ваша ставка для лота <a href="<?= strip_tags($host) . "/lot.php?id=" . strip_tags($lot_id) ?>"><?= strip_tags($lot_name) ?></a> победила.</p>
<p>Перейдите по ссылке <a href="<?= strip_tags($host) ?>/mybids.php">мои ставки</a>,
    чтобы связаться с автором объявления</p>
<small>Интернет Аукцион "YetiCave"</small>
