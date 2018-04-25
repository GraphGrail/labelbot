<?php
/**
 * Created by PhpStorm.
 * User: bytecrow
 * Date: 21.04.2018
 * Time: 19:07
 */
?>
<div class="form-group m-form__group">
    <div class="m-section__sub">
        <?= Yii::t('app', 'Smart contract address') ?>
    </div>
    <div class="form-group field-task-label_group_id">
        <div class="input-group">
            <input type="text" id="address"
                   class="form-control m-input js-contract-address" name="address"
                   value="<?= $address ?>" disabled="disabled">
            <div class="input-group-append">
                <span class="input-group-text">
                    <a href="<?= $link?>"
                     target="_blank"><?= $linkText ?></a>
                </span>
            </div>
        </div>
        <div class="help-block"></div>
    </div>
</div>
