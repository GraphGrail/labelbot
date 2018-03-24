<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Labels';
?>
<div class="row">
	<div class="col-xl-8">
		<!--begin:: Widgets/Support Tickets -->
		<div class="m-portlet m-portlet--full-height ">
			<div class="m-portlet__head">
				<div class="m-portlet__head-caption">
					<div class="m-portlet__head-title">
						<h3 class="m-portlet__head-text">
							<?=Yii::t('app', 'Created Labelings') ?>
						</h3>
					</div>
				</div>
				<div class="m-portlet__head-tools">
					<ul class="m-portlet__nav">
						<li class="m-portlet__nav-item">
							<a href="<?=Url::to(['label/new']) ?>" class="btn btn-info m-btn m-btn--icon">
          						<span>
          							<i class="la la-plus"></i>
          							<span>
          								<?=Yii::t('app', 'Create New Label Group') ?>      							
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
				<div class="m-widget3">
				<?php 
					foreach ($labelGroups as $labelGroup) {
						echo $this->render('_labelGroup', [
							'labelGroup' => $labelGroup
						]);
					}

					if (empty($labelGroups)): 
				?>
					<div class="lead">
						<?=Yii::t('app', 'There is no created Labelings. Please create the new one.'); ?>
					</div>
				<?php endif; ?>
				</div>
			</div>
		</div>
		<!--end:: Widgets/Support Tickets -->
	</div>
</div>
