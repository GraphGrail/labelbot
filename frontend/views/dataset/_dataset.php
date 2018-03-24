<?php
/* @var $this yii\web\View */
$formatter = \Yii::$app->formatter;
?>
<div class="m-widget3__item">
	<div class="m-widget3__header">
		<div class="m-widget3__user-img">
			<img class="m-widget3__img" src="/assets/img/logo/graphgrail.png" alt="">
		</div>
		<div class="m-widget3__info">
			<span class="m-widget3__username">
				<?=$dataset->name ?>
			</span>
			<br>
			<span class="m-widget3__time">
				<?=Yii::t('app', 'Uploded at') ?> <?=$formatter->asDatetime($dataset->created_at, 'long') ?>
			</span>
		</div>
		<span class="m-widget3__status m--font-<?=$dataset->status()->color ?>">
			<?=$dataset->status()->text ?>
		</span>
	</div>
	<div class="m-widget3__body">
		<p class="m-widget3__text">
			<?=$dataset->description ?>
		</p>
	</div>
</div>
