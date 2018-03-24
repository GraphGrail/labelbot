<?php
/* @var $this yii\web\View */
$formatter = \Yii::$app->formatter;
?>
<div class="m-widget3__item">
	<div class="m-widget3__header">
		<div class="m-widget3__info">
			<span class="m-widget3__username">
				<?=$labelGroup->name ?>
			</span>
			<br>
			<span class="m-widget3__time">
				<?=Yii::t('app', 'Created at') ?> <?=$formatter->asDatetime($labelGroup->created_at, 'long') ?>
			</span>
			<p class="m-widget3__text">
				<?=$labelGroup->description ?>
			</p>
		</div>
	</div>
	<div class="m-widget3__body">
	</div>
</div>
