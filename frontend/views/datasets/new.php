<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = Yii::t('app', 'Add New Dataset');
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
              	<?=Yii::t('app', 'Upload your dataset') ?>
              </h5>
              <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class'=>'m-section m--margin-bottom-5']]) ?>
                <div class="m-section__sub">
                	<?=Yii::t('app', 'Dataset name') ?>
                </div>
                <div class="form-group m-form__group">
                  <?= $form->field($model, 'name')
                           ->textInput(['class'=>'form-control m-input'])
                           ->label(false) ?>
                </div>       
                <div class="m-section__sub">
                	<?=Yii::t('app', 'Description') ?>
                </div>
                <div class="form-group m-form__group">
                  <?= $form->field($model, 'description')
                           ->textarea(['class'=>'form-control m-input', 'rows'=>'3'])
                           ->label(false) ?>
                </div>      
                <div class="m-section__sub"><?=Yii::t('app', 'Accepted files in ".csv" format') ?></div>
                <div class="form-group m-form__group">
                  <div class="custom-file">
                  	<?= $form->field($model, 'datasetFile')
                  	         ->fileInput(['class'=>'custom-file-input'])
                  	         ->label(Yii::t('app', 'Choose file'), ['class'=>'custom-file-label']) ?>
                  </div>
                </div>
                <div class="form-group m-form__group m--padding-top-20">
                  <?= Html::submitButton(Yii::t('app', 'Upload'), ['class' => 'btn btn-info m-btn--pill m-btn--air pull-right']) ?>
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
