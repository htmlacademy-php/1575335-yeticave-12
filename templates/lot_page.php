<section class="lot-item container">
    <h2><?= htmlspecialchars($lot['lot_name'] ?? 'Без названия') ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="../<?= htmlspecialchars($lot['img_url'] ?? 'Placeholder.jpg') ?>" width="730" height="548"
                     alt="<?= htmlspecialchars($lot['lot_name'] ?? 'Название лота') ?>">
            </div>
            <p class="lot-item__category">Категория:
                <span><?= htmlspecialchars($lot['category'] ?? 'Категория') ?></span></p>
            <p class="lot-item__description"><?= htmlspecialchars($lot['lot_description'] ?? 'Описание') ?></p>
        </div>
        <div class="lot-item__right">
            <div class="lot-item__state">
                <?php if (isset($lot['lot_closed']) && $lot['lot_closed']): ?>
                    <div class="lot-item__timer timer timer--end">
                        Торги окончены
                    </div>
                <?php else: ?>
                    <div class="lot-item__timer timer
        <?php if (isset($remaining_time[0]) && $remaining_time[0] === '00'): ?>
        timer--finishing
        <?php endif; ?>
        ">
                        <?= (isset($remaining_time[0]) && isset($remaining_time[1])) ? $remaining_time[0] . ":" . $remaining_time[1] : 'Ошибка : Времени' ?>
                    </div>
                <?php endif; ?>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span
                            class="lot-item__cost"><?= htmlspecialchars(format_price(($lot['current_price'] ?? 0) / 100)) ?></span>
                    </div>
                    <div class="lot-item__min-cost">
                        Мин. ставка <span><?= htmlspecialchars(format_price(($lot['min_bid'] ?? 0) / 100)) ?></span>
                    </div>
                </div>
                <?php if ($show_bids): ?>
                    <form class="lot-item__form" action="<?= $_SERVER['REQUEST_URI'] ?>" method="post"
                          autocomplete="off">
                        <p class="lot-item__form-item form__item <?= !empty($errors) ? "form__item--invalid" : "" ?>">
                            <label for="cost">Ваша ставка</label>
                            <input id="cost" type="text" name="cost"
                                   placeholder="<?= htmlspecialchars(($lot['min_bid'] ?? 0) / 100) ?>">
                            <span class="form__error"><?= $errors['cost'] ?? '' ?></span>
                        </p>
                        <button type="submit" class="button">Сделать ставку</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="history">
                <h3>История ставок (<span>10</span>)</h3>
                <table class="history__list">
                    <?php foreach ($bids as $bid): ?>
                        <tr class="history__item">
                            <td class="history__name"><?= htmlspecialchars($bid['user_name'] ?? 'Имя') ?></td>
                            <td class="history__price"><?= htmlspecialchars(format_price(($bid['bid_price'] ?? 0) / 100)) ?></td>
                            <td class="history__time"><?= human_readable_datetime($bid['date_time'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</section>
