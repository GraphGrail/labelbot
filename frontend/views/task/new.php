<?php

use common\models\Dataset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $datasets Dataset[] */
/* @var $labelGroups \common\models\LabelGroup[] */

$this->title = Yii::t('app', 'Add New Task');

$this->registerJs("

", yii\web\View::POS_READY);
?>

<div class="row">
  <div class="col-lg-8">
    <div class="m-portlet m-portlet--tab">
      <div class="m-portlet__body m-portlet__body--no-padding">
        <div class="row m-row--no-padding m-row--col-separator-xl">
          <div class="col-md-12">
            <div class="m-widget1">
              <div class="m--padding-bottom-5"></div>
              <h5 class="m-widget5__title m--margin-bottom-25">
              	<?=Yii::t('app', 'Task details') ?>
              </h5>
              <?php $form = ActiveForm::begin(['options' => ['class'=>'m-section m--margin-bottom-5']]) ?>
                <?= $form->errorSummary($model); ?>
                <div class="m-section__sub">
                	<?=Yii::t('app', 'Task name') ?>
                </div>
                <div class="form-group m-form__group">
                  <?= $form->field($model, 'name')
                           ->textInput(['class'=>'form-control m-input'])
                           ->label(false) ?>
                </div>       
                <div class="m-section__sub">
                	<?=Yii::t('app', 'Description (make it clear, this instruction will appear when labeller get the task to work)') ?>
                </div>
                <div class="form-group m-form__group">
                  <?= $form->field($model, 'description')
                           ->textarea(['class'=>'form-control m-input', 'rows'=>'3'])
                           ->label(false) ?>
                </div>      
                <div class="m-section__sub">
                  <?=Yii::t('app', 'Choose Dataset') ?>
                </div>
                <div class="form-group m-form__group">
                  <div class="custom-file">
                  	<?= $form->field($model, 'dataset_id')
                  	         ->dropDownList(ArrayHelper::map($datasets, 'id', 'name'), ['class'=>'form-control m-input'])
                  	         ->label(false) ?>
                  </div>
                </div>
                <div class="m-section__sub">
                  <?=Yii::t('app', 'Choose Label Group') ?>
                </div>
                <div class="form-group m-form__group">
                  <div class="custom-file">
                    <?= $form->field($model, 'label_group_id')
                             ->dropDownList(ArrayHelper::map($labelGroups, 'id', 'name'), ['class'=>'form-control m-input'])
                             ->label(false) ?>
                  </div>
                </div>
                <div class="form-group m-form__group m--padding-top-20">
                  <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-info m-btn--pill m-btn--air pull-right']) ?>
                  <div class="clearfix"></div>
                </div>
              </form>
              <?php ActiveForm::end() ?>
              <div class="m--padding-bottom-20"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
