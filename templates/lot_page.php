<section class="lot-item container">
    <h2><?= htmlspecialchars($lot['lot_name'] ?? 'Без названия') ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="../<?= htmlspecialchars($lot['img_url'] ?? '#') ?>" width="730" height="548"
                     alt="<?= htmlspecialchars($lot['lot_name'] ?? 'Без названия') ?>">
            </div>
            <p class="lot-item__category">Категория:
                <span><?= htmlspecialchars($lot['name'] ?? 'Без категории') ?></span></p>
            <p class="lot-item__description"><?= htmlspecialchars($lot['lot_description'] ?? 'Описание отсутствует') ?></p>
        </div>
        <div class="lot-item__right">
            <?php if ($is_auth): ?>
                <div class="lot-item__state">
                    <div class="lot-item__timer timer
                    <?php if ($remaining_time[0] == '00'): ?>
                    timer--finishing
                    <?php endif; ?>
                    ">
                        <?= $remaining_time[0] . ":" . $remaining_time[1] ?>
                    </div>
                    <div class="lot-item__cost-state">
                        <div class="lot-item__rate">
                            <span class="lot-item__amount">Текущая цена</span>
                            <span
                                class="lot-item__cost"><?= htmlspecialchars($lot['starting_price'] / 100 ?? 0) ?></span>
                        </div>
                        <div class="lot-item__min-cost">
                            Мин. ставка <span>12 000 р</span>
                        </div>
                    </div>
                    <form class="lot-item__form" action="https://echo.htmlacademy.ru" method="post" autocomplete="off">
                        <p class="lot-item__form-item form__item form__item--invalid">
                            <label for="cost">Ваша ставка</label>
                            <input id="cost" type="text" name="cost" placeholder="12 000">
                            <span class="form__error">Введите наименование лота</span>
                        </p>
                        <button type="submit" class="button">Сделать ставку</button>
                    </form>
                </div>
            <?php endif; ?>
            <!--тут была история ставок -->
        </div>
    </div>
</section>
