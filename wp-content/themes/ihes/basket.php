<?php
	/*
	* Template name: Basket
	*/

	get_header();
	?>
    <section id="basket">
        <div class="cont">
            <h1>Корзина</h1>
            <div class="bask_prod">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/ivashka.png" alt=""><a href=""></a>
                <div class="inf_prod">
                    <div class="up_prod">
                        <div class="name_art">
                            <p class="name_prod">Брюки Energi</p>
                            <p class="art_prod">Артикул: BM-21321</p>
                        </div>
                        <div class="cost_col">
                            <p class="cost">2 900 ₽</p>
                            <div class="col">
                                <label for="col">Количество</label>
                                <input type="number" id="col" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="down_prod">
                        <p class="attr_prod">Размер: XS</p>
                        <a href="">Удалить</a>
                    </div>
                </div>
            </div>
            <div class="bask_prod">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/ivashka.png" alt=""><a href=""></a>
                <div class="inf_prod">
                    <div class="up_prod">
                        <div class="name_art">
                            <p class="name_prod">Брюки Energi</p>
                            <p class="art_prod">Артикул: BM-21321</p>
                        </div>
                        <div class="cost_col">
                            <p class="cost">2 900 ₽</p>
                            <div class="col">
                                <label for="col">Количество</label>
                                <input type="number" id="col" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="down_prod">
                        <p class="attr_prod">Размер: XS</p>
                        <a href="">Удалить</a>
                    </div>
                </div>
            </div>
            <div class="oform">
                <p class="itog">Итоговая сумма: <span>7 000 ₽ </span></p>
                <a href="">ОФОРМИТЬ</a>
            </div>
        </div>
    </section>
	<?php
	get_footer();
