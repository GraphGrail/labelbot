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
    public function getTableSourceAsJson($escape = true)
    {
        $data = json_decode(json_encode($this->getContractStatus()->workers), true);

        $dataWaitsApprovement = array_filter($data, function($worker) {
            return $worker['totalItems'] > ($worker['approvedItems'] + $worker['declinedItems']);
        }, ARRAY_FILTER_USE_BOTH);

        $json = json_encode($dataWaitsApprovement);
        return $escape ? htmlspecialchars($json) : $json;

    }

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