<?php

use app\models\Item;



$icon = isset($model['icon_url']) && $model['icon_url'] ? $model['icon_url'] : '/uploads/photo/default.png';

?>
<script
        src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g="
        crossorigin="anonymous"></script>

<script type="text/javascript">

    $(document).ready(function() {

    });

</script>

    <div class="item uncommon">
        <div class="item__content">
            <div class="item__img">
                <img src="<?= $icon  ?>" class="case-image">
            </div>

            <div class="item__price"><span class="price price-RUB"><?= $model['price']   ?></span></div>
            <div class="item__icons">
                <a href="/case/hole" class="item__icon status linkcase" title="Кейс"></a>
                <div class="item__icon status selled">
                    <span class="tooltip tooltip_center tooltip_extramin">Продано</span>
                </div>
            </div>
            <div class="item__btns">

                <div class="item__btn">
                    <div class="btn btn_color-success btn_size-small btn_uppercase btn_with-icon tosell">
                        <div class="btn__content">
                            <i  style="margin-right: 5px;" class="fa fa-shopping-cart" aria-hidden="true"></i>
                            <div data-id="<?= $model ? $model['id'] :  null ?>"  data-name="<?= $model ? $model['market_hash_name'] :  null ?>" data-price="<?= $model ? $model['price'] :  null ?>" class="btn__label data-price"><span class="price price-RUB"><?= $model ? $model['price'] :  null ?></span></div>
                        </div>
                    </div>
                </div>

                <div class="item__btn">
                    <div class="btn btn_type-square btn_with-icon btn_style-outline btn_color-primary btn_size-small btn_uppercase" action="resendItem">
                        <div class="btn__content">
                            <i style="margin-right: 5px;" class="fa fa-arrow-down" aria-hidden="true"></i>
                            <!-- <div class="btn__label">{{_ "withdraw_items_1"}}</div> -->
                        </div>
                    </div>
                </div>



            </div>

            <div class="item__type-and-name">
                <div class="item__type"><?= $model['type']   ?></div>
                <div class="item__name"><?= $model['market_hash_name']   ?></div>
            </div>
        </div>

    </div>

