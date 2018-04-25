<?php

use common\models\Dataset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $datasets Dataset[] */
/* @var $labelGroups \common\models\LabelGroup[] */

$this->title = Yii::t('app', 'Add New Task');

?>



<div class="row">
  <div class="col-lg-8">
    <div class="m-portlet m-portlet--tab">
      <div class="m-portlet__body m-portlet__body--no-padding">
        <div class="row m-row--no-padding m-row--col-separator-xl">
          <div class="col-md-12">
            <div class="m-widget1">
              <div class="m--padding-bottom-5"></div>
                <?php if ($datasets === [] || $labelGroups === []): ?>
                    <h5 class="m-widget5__title m--margin-bottom-25">
                        <?=Yii::t('app', 'Before creating Task for labeling you need at least one uploaded Dataset and one Labeling.') ?>
                    </h5>
                    <div class="m-stack m-stack--ver m-stack--general m--margin-bottom-40 m--margin-top-40">
                        <?php if ($datasets === []): ?>
                        <div class="m-stack__item m-stack__item--center m-stack__item--middle">
                            <div class="m--margin-bottom-15">
                                <?=Yii::t('app', "You haven't uploaded Datasets. Please upload one."); ?>
                            </div>
                            <a href="/dataset/new" class="btn btn-outline-info btn-md">
                                <?=Yii::t('app', 'Upload Dataset'); ?>
                            </a>
                        </div>
                        <?php endif; ?>
                        <?php if ($labelGroups === []): ?>
                        <div class="m-stack__item m-stack__item--center m-stack__item--middle">
                            <div class="m--margin-bottom-15">
                                <?=Yii::t('app', "You haven't created Labelings. Please create one."); ?>
                            </div>
                            <a href="/label/new" class="btn btn-outline-info btn-md">
                                <?=Yii::t('app', 'Create Labeling'); ?>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
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
                <?php endif; ?>
              <div class="m--padding-bottom-20"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

