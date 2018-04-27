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

    public function getStatusComment()
    {
        $map = [
            Task::STATUS_CONTRACT_ACTIVE_PAUSED        => Yii::t('app',
                'Task paused for scoring and workers don\'t receive new works from this task. In the table below you can 
                either approve current work <span class="m-badge m-badge--success"><i class="la la-check-circle-o"></i></span>, 
                if you check it is done properly, or decline <span class="m-badge m-badge--danger"><i class="la la-ban"></i></span>, 
                if worker done bad. To make a decision you can see a preview of the completed work, which shows part of the labeled data 
                <span class="m-badge m-badge--accent"><i class="la la-picture-o"></i></span>.<br>
                After you score all works push <span class="m-badge m-badge--info m-badge--wide">Send results to blockchain</span> 
                to save results in task\'s  smart contract. Or you can skip scoring and continue the task by clicking 
                on "Continue task" or even ending it by clicking on "Finalize task" in the additional actions menu next 
                the main action button.')
        ];
        if (!array_key_exists($this->task->status, $map)) {
            return parent::getStatusComment();
        }

        return $map[$this->task->status];
    }
}