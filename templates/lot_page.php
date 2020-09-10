
<section class="lot-item container">
  <h2><?= htmlspecialchars($lot['lot_name'] ?? 'Без названия') ?></h2>
  <div class="lot-item__content">
    <div class="lot-item__left">
      <div class="lot-item__image">
        <img src="../<?= htmlspecialchars($lot['img_url'] ?? '#') ?>" width="730" height="548" alt="<?= htmlspecialchars($lot['lot_name'] ?? 'Без названия') ?>">
      </div>
      <p class="lot-item__category">Категория: <span><?= htmlspecialchars($lot['name'] ?? 'Без категории') ?></span></p>
      <p class="lot-item__description"><?= htmlspecialchars($lot['lot_description'] ?? 'Описание отсутствует') ?></p>
    </div>
    <div class="lot-item__right">
      <div class="lot-item__state">
        <div class="lot-item__timer timer
        <?php if($remaining_time[0]=='00'):?>
        timer--finishing
        <?php endif; ?>
        ">
          <?=$remaining_time[0] . ":" . $remaining_time[1] ?>
        </div>
        <div class="lot-item__cost-state">
          <div class="lot-item__rate">
            <span class="lot-item__amount">Текущая цена</span>
            <span class="lot-item__cost"><?= htmlspecialchars($lot['starting_price']/100 ?? 0) ?></span>
          </div>
          <div class="lot-item__min-cost">
            Мин. ставка <span>12 000 р</span>
          </div>
        </div>
        <!--тут была форма и история ставок -->
    </div>
  </div>
</section>
