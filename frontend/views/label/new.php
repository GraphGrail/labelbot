<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = Yii::t('app', 'Create New Labeling');

$this->registerCss("
#labels-tree { margin: -15px 0 15px -50px; }
#labels-tree .label { margin: 10px 0 0 20px; }
#labels-tree .label-0 > .input-group { display: none; }
");

$this->registerJs("
const form = $('#add_label');

$('.js-add-child').on('click', function(e) {
  const parent = $(this).closest('.label');
  const label  = parent.clone(true);
  const level  = $(parent).data('level') + 1;

  $(label).removeClass('label-0');
  $(label).children('.input-group').children('input').val('');
  $(label).children('.child-labels').html('');
  $(label).attr('data-level', level);

  $(label).appendTo( $(parent).children('.child-labels') );
});

$('.js-add').on('click', function(e) {
  $('#labels-tree').find('.label :first').find('.js-add-child :first').trigger('click');
});

$('.js-del-label').on('click', function(e) {
  e.preventDefault();
  $(this).closest('.label').remove();
});

$('.js-save').on('click', function(e) {
  e.preventDefault();
  const labelsTree = createLabelTreeRecursievly($('.label-0').children('.child-labels'));
  $('#labelgroup-labels_tree').val(labelsTree.length < 1 ? '' : JSON.stringify(labelsTree));
  
  form.validate({
    rules: {
        'LabelGroup[name]': {
            required: true,
        },
        'LabelGroup[labels_tree]': {
            required: true
        }
    }
  });
  
  if (form.valid()) {
    $(this).submit()  
  }
});

function createLabelTreeRecursievly(childLabels) {
  let labels = [];
  $(childLabels).children('.label').each(function() {
    const text = $(this).children('.input-group').children('input').val();
    if (text.trim()==='') return;
    const childLabels = $(this).children('.child-labels');
    labels.push({[text]: createLabelTreeRecursievly(childLabels)});
  });
  return labels;
}

const exampleLabeling = function() {
    let examples = [
        '',
        'Payment',
        'Card',
        'Question how to pay',
        'Shipment',
        'Time of shipment',
        'Price',
        'Other'
    ];
    
    $('.js-add').trigger('click').trigger('click');
    $('.js-add-child').trigger('click').trigger('click');
    $.each($('.label input'), function () { $(this).val(examples.shift())});
} ();

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
              	<?=Yii::t('app', 'Create labels tree here') ?>
              </h5>
              <?php $form = ActiveForm::begin(['options' => ['id' => 'add_label', 'class'=>'m-section m--margin-bottom-5']]) ?>
                <div class="m-section__sub">
                	<?=Yii::t('app', 'Labels tree name') ?>
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
  	            <div class="m-section__sub"><?=Yii::t('app', 'Create labels tree here') ?></div>
                  <div class="col-md-8">
  	                <div class="form-group m-form__group">
  	                  <?= $form->field($model, 'labels_tree')
  	                           ->hiddenInput(['class'=>'form-control m-input'])
  	                           ->label(false) ?>
  	                </div>
  	            </div>
                    <div id="labels-tree"  class="form-group  m-form__group row">
                        <div class="col-lg-10">
                    <div data-level="0" class="label label-0">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <button class="btn btn-warning js-add-child" type="button">
                            <span>
                              <i class="la la-plus"></i>
                              <span>
                                <?=Yii::t('app', 'Child') ?>
                              </span>
                            </span>
                          </button>
                        </div>
                        <input type="text" class="form-control form-control-danger" placeholder="Enter Label Text">
                        <div class="input-group-append">
                          <a href="#" class="btn btn-danger m-btn m-btn--icon js-del-label">
                            <i class="la la-close"></i>
                          </a>
                        </div>
                      </div>
                      <div class="child-labels"></div>
                    </div>
                </div>
                </div>
          			<div class="row">
          				<div class="col-lg-1"></div>
          				<div class="col">
          					<div class="btn btn btn-warning m-btn m-btn--icon js-add">
          						<span>
          							<i class="la la-plus"></i>
          							<span>
          								<?=Yii::t('app', 'Add') ?>
          							</span>
          						</span>
          					</div>
          				</div>
          			</div>
                <div class="form-group m-form__group m--padding-top-20">
                  <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-info m-btn--pill m-btn--air pull-right js-save']) ?>
                  <div class="clearfix"></div>
                </div>
              <?php ActiveForm::end() ?>
              <div class="m--padding-bottom-20"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

