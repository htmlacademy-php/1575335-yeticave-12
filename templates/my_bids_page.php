<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        <?php foreach ($mybids as $bid): ?>
            <tr class="rates__item
      <?php if (isset($bid['lot_closed']) && $bid['lot_closed']): ?>
      rates__item--end
      <?php elseif (isset($bid['winner']) && $bid['winner']): ?>
      rates__item--win
      <?php endif; ?>">
                <td class="rates__info">
                    <div class="rates__img">
                        <img src="../<?= htmlspecialchars($bid['img_url']) ?>" width="54" height="40" alt="Сноуборд">
                    </div>
                    <div>
                        <h3 class="rates__title"><a
                                href="lot.php?id=<?= $bid['lot_id'] ?>"><?= htmlspecialchars($bid['lot_name']) ?></a>
                        </h3>
                        <?php if (isset($bid['winner']) && $bid['winner']): ?>
                            <p><?= $bid['contacts'] ?></p>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="rates__category">
                    <?= htmlspecialchars($bid['category']) ?>
                </td>
                <?php if (isset($bid['winner']) && $bid['winner']): ?>
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
        <?php if ($remaining_time[0] == '00'): ?>
        timer--finishing
        <?php endif; ?>
        ">
                            <?= $bid['remaining_time'][0] . ":" . $bid['remaining_time'][1] ?>
                        </div>
                    </td>
                <?php endif; ?>
                <td class="rates__price">
                    <?= htmlspecialchars(format_price($bid['bid_price'] / 100 ?? 0)) ?>
                </td>
                <td class="rates__time">
                    <?= human_readable_datetime($bid['date_time'] ?? '') ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
