<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BlockchainCallbackSearch represents the model behind the search form of `common\models\BlockchainCallback`.
 */
class BlockchainCallbackSearch extends BlockchainCallback
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at'], 'integer'],
            [['type', 'params', 'callback_id', 'received', 'success', 'error', 'payload'], 'safe'],
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
        $query = BlockchainCallback::find();

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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'params', $this->params])
            ->andFilterWhere(['like', 'callback_id', $this->callback_id])
            ->andFilterWhere(['like', 'received', $this->received])
            ->andFilterWhere(['like', 'success', $this->success])
            ->andFilterWhere(['like', 'error', $this->error])
            ->andFilterWhere(['like', 'payload', $this->payload]);

        return $dataProvider;
    }
}
