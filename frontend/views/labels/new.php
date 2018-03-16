<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = Yii::t('app', 'Create New Label Group');

$this->registerJs("

$('#m_repeater').repeater({            
    initEmpty: false,
   
    defaultValues: {
        'text-input': 'foo'
    },
     
    show: function() {
        $(this).slideDown();                               
    },

    hide: function(deleteElement) {                 
        if(confirm('Are you sure you want to delete this element?')) {
            $(this).slideUp(deleteElement);
        }                                
    }      
});

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
              	<?=Yii::t('app', 'Create label group') ?>
              </h5>
              <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class'=>'m-section m--margin-bottom-5']]) ?>
                <div class="m-section__sub">
                	<?=Yii::t('app', 'Labels group name') ?>
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
	                           ->textInput(['class'=>'form-control m-input'])  // hiddenInput
	                           ->label(false) ?>
	                </div>
	            </div>


				<div id="m_repeater">
					<div class="form-group  m-form__group row">
						<div data-repeater-list="" class="col-lg-10">
							<div data-repeater-item="" class="m--margin-bottom-10">
								<div class="input-group">
									<div class="input-group-prepend">
										<button class="btn btn-warning" type="button">
											<span>
												<i class="la la-plus"></i>
												<span>
													Child
												</span>
											</span>
										</button>
									</div>
									<input type="text" class="form-control form-control-danger" placeholder="Enter Label Text">
									<div class="input-group-append">
										<a href="#" class="btn btn-danger m-btn m-btn--icon">
											<i class="la la-close"></i>
										</a>
									</div>
								</div>
							</div>

						</div>
					</div>
					<div class="row">
						<div class="col-lg-1"></div>
						<div class="col">
							<div data-repeater-create="" class="btn btn btn-warning m-btn m-btn--icon">
								<span>
									<i class="la la-plus"></i>
									<span>
										Add
									</span>
								</span>
							</div>
						</div>
					</div>
				</div>



                <div class="form-group m-form__group m--padding-top-20">
                  <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-info m-btn--pill m-btn--air pull-right']) ?>
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

