<section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <!--заполните этот список из массива категорий-->
			<?php foreach($categories as $category):?>
            <li class="promo__item promo__item--<?= $category['symbol_code'] ?>">
                <a class="promo__link" href="pages/all-lots.html"><?= htmlspecialchars($category['name']) ?></a>
            </li>
			<?php endforeach;?>
        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">
            <!--заполните этот список из массива с товарами-->
			<?php foreach( $items as $item):?>
            <?php $remaining_time = get_time_remaining($item['expiration_date']) ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?= htmlspecialchars($item['img_url']) ?>" width="350" height="260" alt="">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?= htmlspecialchars($item['category']) ?></span>
                    <h3 class="lot__title"><a class="text-link" href="pages/lot.html"><?= htmlspecialchars($item['name']) ?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?= htmlspecialchars(format_price($item['price']/100)) ?></span>
                        </div>
                        <div class="lot__timer timer
                        <?php if($remaining_time[0]=='00'):?>
                        timer--finishing
                        <?php endif; ?>
                        ">
                            <?= $remaining_time[0] . ':' . $remaining_time[1] ?>
                        </div>
                    </div>
                </div>
            </li>
			<?php endforeach;?>
        </ul>
    </section>