<div class="container">
    <section class="lots">
        <h2><?php if (!empty($items)): ?>
                Результаты поиска по запросу «<span><?= $search_query ?? '' ?></span>»
            <?php else: ?>
                Ничего не найдено по вашему запросу
            <?php endif; ?>
        </h2>
        <ul class="lots__list">
            <?php foreach ($items as $item): ?>
                <?php $remaining_time = get_time_remaining($item['date_end']) ?>
                <li class="lots__item lot">
                    <div class="lot__image">
                        <img src="../<?= htmlspecialchars($item['img_url']) ?>" width="350" height="260" alt="Сноуборд">
                    </div>
                    <div class="lot__info">
                        <span class="lot__category"><?= htmlspecialchars($item['category']) ?></span>
                        <h3 class="lot__title"><a class="text-link"
                                                  href="lot.php?id=<?= $item['lot_id'] ?>"><?= htmlspecialchars($item['lot_name']) ?></a>
                        </h3>
                        <div class="lot__state">
                            <div class="lot__rate">
                                <span class="lot__amount">Стартовая цена</span>
                                <span
                                    class="lot__cost"><?= htmlspecialchars(format_price($item['starting_price'] / 100)) ?></span>
                            </div>
                            <?php if ($remaining_time[0] == '00' && $remaining_time[1] == '00'): ?>
                                <div class="lot__timer timer">
                                    Торги окончены
                                </div>
                            <?php else: ?>
                                <div class="lot__timer timer
                            <?php if ($remaining_time[0] == '00'): ?>
                                timer--finishing
                            <?php endif; ?>
                                ">
                                    <?= $remaining_time[0] . ':' . $remaining_time[1] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php if (!empty($pages) && !empty($items)): ?>
        <ul class="pagination-list">

            <li class="pagination-item pagination-item-prev">
                <?= isset($pages[0]) ? "<a href=\"search.php?search=$search_query&page=$pages[0]\">Назад</a>" : "<a style=\"visibility:hidden\">Назад</a>" ?>
            </li>
            <?php for ($i = 1; $i <= $num_pages; $i++): ?>
                <li class="pagination-item <?= $i == $pages[1] ? "pagination-item-active" : "" ?>">
                    <a <?= $i != $pages[1] ? "href=\"search.php?search=$search_query&page=$i\"" : "" ?> ><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="pagination-item pagination-item-next">
                <?= isset($pages[2]) ? "<a href=\"search.php?search=$search_query&page=$pages[2]\">Вперед</a>" : "<a style=\"visibility:hidden\">Вперед</a>" ?>
            </li>
        </ul>
    <?php endif; ?>
</div>
