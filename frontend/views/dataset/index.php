<?php

use frontend\assets\pages\DatasetPageAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $datasets \common\models\Dataset[] */
$this->title = 'Datasets';

DatasetPageAsset::register($this);

$showHelper = false;

?>
<div class="row">
	<div class="col-xl-8">
		<!--begin:: Widgets/Support Tickets -->
		<div class="m-portlet m-portlet--full-height ">
			<div class="m-portlet__head">
				<div class="m-portlet__head-caption">
					<div class="m-portlet__head-title">
						<h3 class="m-portlet__head-text">
							<?=Yii::t('app', 'Uploaded Datasets') ?>
						</h3>
					</div>
				</div>
				<div class="m-portlet__head-tools">
					<ul class="m-portlet__nav">
						<li class="m-portlet__nav-item">
							<a href="<?=Url::to(['dataset/new']) ?>" class="btn btn-info m-btn m-btn--icon">
          						<span>
          							<i class="la la-plus"></i>
          							<span>
          								<?=Yii::t('app', 'Add New Dataset') ?>      							
          							</span>
          						</span>
          					</a>
						</li>
						<!--li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
							<a href="#" class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl m-dropdown__toggle">
								<i class="la la-ellipsis-h m--font-brand"></i>
							</a>
							<div class="m-dropdown__wrapper">
								<span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
								<div class="m-dropdown__inner">
									<div class="m-dropdown__body">
										<div class="m-dropdown__content">
											<ul class="m-nav">
												<li class="m-nav__item">
													<a href="" class="m-nav__link">
														<i class="m-nav__link-icon flaticon-share"></i>
														<span class="m-nav__link-text">
															Show deleted
														</span>
													</a>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</li-->
					</ul>
				</div>
			</div>
			<div class="m-portlet__body">
                <div class="m-widget4 m-widget4--progress">
				<?php 
					foreach ($datasets as $dataset) {
					    if ($dataset->status === \common\models\Dataset::STATUS_READY) {
					        $showHelper = true;
                        }
						echo $this->render('_dataset', [
							'dataset' => $dataset
						]);
					}

					if (empty($datasets)): 
				?>
					<div class="lead">
						<?=Yii::t('app', 'There is no uploaded Datasets. Please upload the new one.'); ?>
					</div>
                        <div class="m-stack m-stack--ver m-stack--general m--padding-top-30 m--padding-bottom-30">
                            <div class="m-stack__item m-stack__item--center m-stack__item--middle">
                                <a href="/dataset/new" class="btn btn-info btn-lg">
                                    <?=Yii::t('app', 'Upload Dataset'); ?>
                                </a>
                            </div>
                        </div>
				<?php endif; ?>
				</div>
			</div>
		</div>
		<!--end:: Widgets/Support Tickets -->
    </div>
    <?php if ($showHelper): ?>
    <div class="col-xl-8">
        <div class="m-alert m-alert--icon alert alert-success js-first-dataset" role="alert" style="">
            <div class="m-alert__icon">
                <i class="flaticon-information"></i>
            </div>
            <div class="m-alert__text js-credit-text">
                <?=Yii::t('app', "You have ready datasets. Now you can create Labeling, if you didn't before or you need a new one. Or go to Task creation and create a new Task.") ?>
            </div>
            <div class="m-alert__actions credit-action" style="width: 260px;">
                <a class="btn btn-link btn-outline-light btn-sm m-btn m-btn--hover-secondary" href="/label/new">
                    <?=Yii::t('app', "Create Labeling") ?>
                </a>
                <a class="btn btn-link btn-outline-light btn-sm m-btn m-btn--hover-secondary" href="/task/new">
                    <?=Yii::t('app', "Create Task") ?>
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>


<div class="modal fade" id="delete_dataset_modal" tabindex="-1" role="dialog" aria-labelledby="delete_dataset_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Delete dataset
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        &times;
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    Are you sure?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary break-delete-link" data-dismiss="modal">
                    No
                </button>
                <a href="<?=Url::toRoute('dataset/delete')?>" class="btn btn-danger confirm-delete-link">
                    <i class="fa fa-trash-o"></i>
                    Yes
                </a>
            </div>
        </div>
    </div>
</div>