<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TaskSearch represents the model behind the search form of `common\models\Task`.
 */
class TaskSearch extends Task
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'dataset_id', 'label_group_id', 'work_item_size', 'total_work_items', 'created_at', 'updated_at'], 'integer'],
            [['name', 'description', 'contract_address', 'contract', 'status', 'deleted', 'delivering_job_id', 'result_file'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Task::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'dataset_id' => $this->dataset_id,
            'label_group_id' => $this->label_group_id,
            'work_item_size' => $this->work_item_size,
            'total_work_items' => $this->total_work_items,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'contract_address', $this->contract_address])
            ->andFilterWhere(['like', 'contract', $this->contract])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'deleted', $this->deleted])
            ->andFilterWhere(['like', 'delivering_job_id', $this->delivering_job_id])
            ->andFilterWhere(['like', 'result_file', $this->result_file]);

        return $dataProvider;
    }
}
