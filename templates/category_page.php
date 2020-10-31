<div class="container">
    <section class="lots">
        <h2>Все лоты в категории <span><?= $category_name ?></span></h2>
        <ul class="lots__list">
            <?php foreach ($items as $item): ?>
                <li class="lots__item lot">
                    <div class="lot__image">
                        <img src="../<?= htmlspecialchars($item['img_url'] ?? 'placeholder.jpg') ?>" width="350"
                             height="260" alt="<?= htmlspecialchars($item['lot_name'] ?? 'Название лота') ?>>">
                    </div>
                    <div class="lot__info">
                        <span
                            class="lot__category"><?= htmlspecialchars($item['category'] ?? 'Категория лота') ?></span>
                        <h3 class="lot__title"><a class="text-link"
                                                  href="lot.php?id=<?= htmlspecialchars($item['lot_id'] ?? 0) ?>"><?= htmlspecialchars($item['lot_name'] ?? 'Название лота') ?></a>
                        </h3>
                        <div class="lot__state">
                            <div class="lot__rate">
                                <?php if (isset($item['bid_count']) && $item['bid_count'] > 0): ?>
                                    <span
                                        class="lot__amount"><?= $item['bid_count'] . " " . get_noun_plural_form($item['bid_count'],
                                            'ставка', 'ставки', 'ставок') ?></span>
                                    <span
                                        class="lot__cost"><?= htmlspecialchars(format_price(($item['bid_price'] ?? 0) / 100)) ?></span>
                                <?php else: ?>
                                    <span class="lot__amount">Стартовая цена</span>
                                    <span
                                        class="lot__cost"><?= htmlspecialchars(format_price(($item['price'] ?? 0) / 100)) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="lot-item__timer timer
                        <?php if (isset($item['remaining_time']) && isset($item['remaining_time'][0], $item['remaining_time'][1]) && $item['remaining_time'][0] === '00'): ?>
                        timer--finishing
                        <?php endif; ?>
                        ">
                                <?= (isset($item['remaining_time'][0], $item['remaining_time'][1])) ? $item['remaining_time'][0] . ":" . $item['remaining_time'][1] : "Ошибка : Времени" ?>
                            </div>
                        </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php if (!empty($pages) && !empty($items)): ?>
        <ul class="pagination-list">

            <li class="pagination-item pagination-item-prev">
                <?= isset($pages[0]) ? "<a href=\"category.php?id=$category_id&page=$pages[0]\">Назад</a>" : "<a style=\"visibility:hidden\">Назад</a>" ?>
            </li>
            <?php for ($i = 1; $i <= $num_pages; $i++): ?>
                <li class="pagination-item <?= $i === $pages[1] ? "pagination-item-active" : "" ?>">
                    <a <?= (isset($pages[1]) && $i !== $pages[1]) ? "href=\"category.php?id=$category_id&page=$i\"" : "" ?> ><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="pagination-item pagination-item-next">
                <?= isset($pages[2]) ? "<a href=\"category.php?id=$category_id&page=$pages[2]\">Вперед</a>" : "<a style=\"visibility:hidden\">Вперед</a>" ?>
            </li>
        </ul>
    <?php endif; ?>
</div>
