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
                $data[$moderatorId] = [
                    'totalItems' => 0,
                    'approvedItems' => 0,
                    'declinedItems' => 0,
                ];
            }
            $data[$moderatorId]['current'] = sprintf('%s/%s (%s)', $count, $this->task->work_item_size, \Yii::$app->getFormatter()->asPercent($count / $this->task->work_item_size));
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
            case Task::STATUS_CONTRACT_FINALIZED:
                $action = new ActionView(
                    Yii::t('app', 'Download result') . ' <i class="la la-download"></i>',
                    Url::toRoute(['task/download-result', 'id' => $this->task->id])
                );
                $action->setOptions([
                    'class' => 'btn btn-accent m-btn--pill m-btn--air',
                ]);
                break;
        }
        return $action;
    }

    /**
     * @return ActionView[]
     */
    public function getAdditionalActions(): array
    {
        $actions = [];

        $taskPaused = in_array($this->task->status, [
            Task::STATUS_CONTRACT_ACTIVE_PAUSED
        ]);

        if ($taskPaused) {
            $action = new ActionView(
                Yii::t('app', 'Continue task')
            );
            $action->setOptions([
                'class' => 'js-btn-release',
                'iconClass' => 'la la-play',
            ]);
            $actions[] = $action;
        }

        $taskCanBeFinalizedByUser = in_array($this->task->status, [
            Task::STATUS_CONTRACT_NEW_NEED_TOKENS,
            Task::STATUS_CONTRACT_NEW,
            Task::STATUS_CONTRACT_ACTIVE_PAUSED,
            Task::STATUS_CONTRACT_ACTIVE_COMPLETED,
        ]);

        if ($taskCanBeFinalizedByUser) {
            $action = new ActionView(
                Yii::t('app', 'Finalize task')
            );
            $action->setOptions([
                'class' => 'finalize-task-btn',
                'iconClass' => 'la la-check',
            ]);
            $actions[] = $action;
        }

        return $actions;
    }

    public function getStatusColor()
    {
        $map = [
            Task::STATUS_CONTRACT_NOT_DEPLOYED         => 'primary',
            Task::STATUS_CONTRACT_DEPLOYMENT_PROCESS   => 'primary',
            Task::STATUS_CONTRACT_DEPLOYMENT_ERROR     => 'danger',
            Task::STATUS_CONTRACT_NEW_NEED_TOKENS      => 'danger',
            Task::STATUS_CONTRACT_NEW                  => 'primary',
            Task::STATUS_CONTRACT_ACTIVE               => 'primary',
            Task::STATUS_CONTRACT_ACTIVE_NEED_TOKENS   => 'danger',
            Task::STATUS_CONTRACT_ACTIVE_WAITING_PAUSE => 'warning',
            Task::STATUS_CONTRACT_ACTIVE_PAUSED        => 'warning',
            Task::STATUS_CONTRACT_ACTIVE_COMPLETED     => 'primary',
            Task::STATUS_CONTRACT_FORCE_FINALIZING     => 'primary',
            Task::STATUS_CONTRACT_FINALIZED            => 'success',
        ];
        if (!array_key_exists($this->task->status, $map)) {
            return 'info';
        }

        return $map[$this->task->status];
    }

    public function getStatusLabel()
    {
        $map = [
            Task::STATUS_CONTRACT_NOT_DEPLOYED         => 'Not deployed',
            Task::STATUS_CONTRACT_DEPLOYMENT_PROCESS   => 'Deployment process',
            Task::STATUS_CONTRACT_DEPLOYMENT_ERROR     => 'Deployment error',
            Task::STATUS_CONTRACT_NEW_NEED_TOKENS      => 'Need tokens',
            Task::STATUS_CONTRACT_NEW                  => 'New contract',
            Task::STATUS_CONTRACT_ACTIVE               => 'Active',
            Task::STATUS_CONTRACT_ACTIVE_NEED_TOKENS   => 'Need tokens',
            Task::STATUS_CONTRACT_ACTIVE_WAITING_PAUSE => 'Pausing',
            Task::STATUS_CONTRACT_ACTIVE_PAUSED        => 'Paused',
            Task::STATUS_CONTRACT_ACTIVE_COMPLETED     => 'Completed',
            Task::STATUS_CONTRACT_FORCE_FINALIZING     => 'Finalizing',
            Task::STATUS_CONTRACT_FINALIZED            => 'Finalized',
        ];
        if (!array_key_exists($this->task->status, $map)) {
            return '';
        }

        return $map[$this->task->status];
    }

    public function getStatusComment()
    {
        $map = [
            Task::STATUS_CONTRACT_NOT_DEPLOYED         => '',
            Task::STATUS_CONTRACT_DEPLOYMENT_PROCESS   => '',
            Task::STATUS_CONTRACT_DEPLOYMENT_ERROR     => '',
            Task::STATUS_CONTRACT_NEW_NEED_TOKENS      => '',
            Task::STATUS_CONTRACT_NEW                  => '',
            Task::STATUS_CONTRACT_ACTIVE               => Yii::t('app',
                'Now navigate to <a href="http://telegram.me/datalabelbot" target="_blank">@DataLabelBot in Telegram</a>, 
                 login with your Ethereum address and type command /tasks to check your task successfully distributed 
                 to workers of the Platform. The table below shows the progress of the data labeling. 
                 When some of the employees complete their current work by 100%, you can click on the 
                 <span class="m-badge m-badge--accent m-badge--wide">Score work</span> 
                 button to pause the task and approve or decline their work.'),
            Task::STATUS_CONTRACT_ACTIVE_NEED_TOKENS   => '',
            Task::STATUS_CONTRACT_ACTIVE_WAITING_PAUSE => '',
            Task::STATUS_CONTRACT_ACTIVE_PAUSED        => Yii::t('app',
                'Task paused for scoring and workers don\'t receive new works from this task. Push 
                <span class="m-badge m-badge--accent m-badge--wide">Score work</span> button
                to approve works. Or you can skip scoring and continue the task by clicking 
                on "Continue task" or even ending it by clicking on "Finalize task" in the additional actions menu next 
                the main action button.'),
            Task::STATUS_CONTRACT_ACTIVE_COMPLETED     => Yii::t('app',
                'All works completed. Click on <span class="m-badge m-badge--danger m-badge--wide">Finalize task</span> 
                button to finalize smart contract.'),
            Task::STATUS_CONTRACT_FORCE_FINALIZING     => '',
            Task::STATUS_CONTRACT_FINALIZED            => Yii::t('app',
                'Done! Now data-labeler can get paid with GAI token for work.<br>
                 You can download ready to use dataset. All the data now labeled with categories, you provided to worker.<br>
                 Now you can use dataset to train neural networks for your business-task.'),
        ];
        if (!array_key_exists($this->task->status, $map)) {
            return '';
        }

        return $map[$this->task->status];
    }
}