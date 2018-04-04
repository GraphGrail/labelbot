<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace common\models\view;


use common\models\Task;
use Yii;
use yii\helpers\Url;

class TaskDetailView
{

    protected $task;
    protected $contractStatus;
    protected $approvedCount = 0;
    protected $fullCount;

    protected $moderatorAssignedCount = [];

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * @param mixed $approvedCount
     * @return TaskDetailView
     */
    public function setApprovedCount($approvedCount)
    {
        $this->approvedCount = $approvedCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getApprovedCount()
    {
        return $this->approvedCount;
    }

    public function getCompletedPercent()
    {
        return \Yii::$app->getFormatter()->asPercent($this->getApprovedCount() / $this->getFullCount());
    }

    /**
     * @return Task
     */
    public function getTask(): Task
    {
        return $this->task;
    }

    /**
     * @param mixed $contractStatus
     * @return TaskDetailView
     */
    public function setContractStatus($contractStatus)
    {
        $this->contractStatus = $contractStatus;
        return $this;
    }

    /**
     * @return \stdClass
     */
    public function getContractStatus()
    {
        return $this->contractStatus;
    }

    public function getTableSourceAsJson($escape = true)
    {
        $data = json_decode(json_encode($this->getContractStatus()->workers), true);
        foreach ($this->moderatorAssignedCount as $moderatorId => $count) {
            if (!array_key_exists($moderatorId, $data)) {
                continue;
            }
            $data[$moderatorId]['current'] = sprintf('%s/%s(%s)', $count, $this->task->work_item_size, \Yii::$app->getFormatter()->asPercent($count / $this->task->work_item_size));
        }

        $json = json_encode($data);
        if ($escape) {
            return htmlspecialchars($json);
        }
        return $json;
    }

    public function getName()
    {
        return $this->task->name;
    }

    public function getWorkSize()
    {
        return $this->task->work_item_size;
    }

    public function addModeratorAssignedCount($moderatorId, $count)
    {
        $this->moderatorAssignedCount[$moderatorId] = $count;
        return $this;
    }

    public function getModeratorAssignedCount($moderatorId)
    {
        if (!array_key_exists($moderatorId, $this->moderatorAssignedCount)) {
            return 0;
        }
        return $this->moderatorAssignedCount[$moderatorId];
    }

    /**
     * @return mixed
     */
    public function getFullCount()
    {
        return $this->fullCount;
    }

    /**
     * @param mixed $fullCount
     * @return TaskDetailView
     */
    public function setFullCount($fullCount)
    {
        $this->fullCount = $fullCount;
        return $this;
    }

    public function getNextAction(): ?ActionView {
        $action = null;
        $status = $this->task->status;
        switch ($status) {
            case Task::STATUS_CONTRACT_ACTIVE:
            case Task::STATUS_CONTRACT_ACTIVE_PAUSED:
                $action = new ActionView(\
                    Yii::t('app', 'Score work') . ' <i class="la la-pause"></i>',
                    Url::toRoute(['task/score-work', 'id' => $this->task->id]));
                $action->setOptions([
                    'class' => 'btn btn-accent m-btn--pill m-btn--air',
                ]);
                break;
            case Task::STATUS_CONTRACT_NEW_NEED_TOKENS:
                $action = new ActionView(
                    Yii::t('app', 'Add tokens') . ' <i class="la la-plus-circle"></i>',
                    Url::toRoute(['task/smart-contract', 'id' => $this->task->id])
                );
                $action->setOptions([
                    'class' => 'btn btn-danger m-btn--pill m-btn--air',
                ]);
                break;
            case Task::STATUS_CONTRACT_ACTIVE_NEED_TOKENS:
                $action = new ActionView(
                    Yii::t('app', 'Add tokens') . ' <i class="la la-plus-circle"></i>',
                    Url::toRoute(['task/smart-contract', 'id' => $this->task->id])
                );
                $action->setOptions([
                    'class' => 'btn btn-danger m-btn--pill m-btn--air',
                ]);
                break;
            case Task::STATUS_CONTRACT_ACTIVE_COMPLETED:
                $action = new ActionView(
                    Yii::t('app', 'Finalize task') . ' <i class="la la-check"></i>'
                );
                $action->setOptions([
                    'class' => 'btn btn-danger m-btn--pill m-btn--air finalize-task-btn',
                ]);
                break;

            case Task::STATUS_CONTRACT_NOT_DEPLOYED:
                $action = new ActionView(
                    Yii::t('app', 'Create Smart-contract') . ' <i class="la la-plus-circle"></i>',
                    Url::toRoute(['task/smart-contract', 'id' => $this->task->id])
                );
                $action->setOptions([
                    'class' => 'btn btn-success m-btn--pill m-btn--air',
                ]);
                break;

            case Task::STATUS_CONTRACT_DEPLOYMENT_PROCESS:
                $action = new ActionView(
                    Yii::t('app', 'On deployment') . ' <i class="la la-plane"></i>',
                    Url::toRoute(['task/smart-contract', 'id' => $this->task->id])
                );
                $action->setOptions([
                    'class' => 'btn btn-success m-btn--pill m-btn--air',
                ]);
                break;

            case Task::STATUS_CONTRACT_NEW:
                $action = new ActionView(
                    Yii::t('app', 'Activate smart contract') . ' <i class="la la-play"></i>',
                    Url::toRoute(['task/smart-contract', 'id' => $this->task->id])
                );
                $action->setOptions([
                    'class' => 'btn btn-accent m-btn--pill m-btn--air',
                ]);
                break;
        }
        return $action;
    }
}