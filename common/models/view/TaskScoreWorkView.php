<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace common\models\view;


use common\models\Task;
use Yii;
use yii\helpers\Url;

class TaskScoreWorkView extends TaskDetailView
{

    public function getNextAction(): ?ActionView {
        $action = null;
        $status = $this->task->status;
        switch ($status) {
            case Task::STATUS_CONTRACT_ACTIVE_PAUSED:
                $action = new ActionView(
                    Yii::t('app', 'Continue task') . ' <i class="la la-play"></i>',
                    Url::toRoute(['task/release', 'id' => $this->task->id])
                );
                $action->setOptions([
                    'class' => 'btn btn-success m-btn--pill m-btn--air',
                ]);
                break;

            default:
                $action = parent::getNextAction();
                break;
        }
        return $action;
    }
}