<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        <?php foreach ($mybids as $bid): ?>
            <tr class="rates__item
      <?php if (isset($bid['is_winner']) && $bid['is_winner']): ?>
      rates__item--win
      <?php elseif (isset($bid['lot_closed']) && $bid['lot_closed']): ?>
      rates__item--end
      <?php endif; ?>">
                <td class="rates__info">
                    <div class="rates__img">
                        <img src="../<?= htmlspecialchars($bid['img_url'] ?? 'placeholder.jpg') ?>" width="54"
                             height="40" alt="<?= htmlspecialchars($bid['lot_name'] ?? 'Название лота') ?> ">
                    </div>
                    <div>
                        <h3 class="rates__title"><a
                                href="lot.php?id=<?= $bid['lot_id'] ?>"><?= htmlspecialchars($bid['lot_name'] ?? 'Название лота') ?></a>
                        </h3>
                        <?php if (isset($bid['is_winner']) && $bid['is_winner']): ?>
                            <p><?= htmlspecialchars($bid['contacts'] ?? 'Контакты отсутствуют') ?></p>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="rates__category">
                    <?= htmlspecialchars($bid['category'] ?? 'Категория') ?>
                </td>
                <?php if (isset($bid['is_winner']) && $bid['is_winner']): ?>
                    <td class="rates__timer">
                        <div class="timer timer--win">Ставка выиграла</div>
                    </td>
                <?php elseif (isset($bid['lot_closed']) && $bid['lot_closed']): ?>
                    <td class="rates__timer">
                        <div class="timer timer--end">Торги окончены</div>
                    </td>
                <?php else: ?>
                    <td class="rates__timer">
                        <div class="timer
        <?php if (isset($bid['remaining_time'][0]) && $bid['remaining_time'][0] === '00'): ?>
        timer--finishing
        <?php endif; ?>
        ">
                            <?= (isset($bid['remaining_time'][0]) && isset($bid['remaining_time'][1])) ? $bid['remaining_time'][0] . ":" . $bid['remaining_time'][1] : 'Ошибка : Времени' ?>
                        </div>
                    </td>
                <?php endif; ?>
                <td class="rates__price">
                    <?= htmlspecialchars(format_price(($bid['bid_price'] ?? 0) / 100)) ?>
                </td>
                <td class="rates__time">
                    <?= human_readable_datetime(strip_tags($bid['date_time'] ?? '')) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
