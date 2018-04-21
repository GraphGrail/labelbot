<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Moderator;

/**
 * ModeratorSearch represents the model behind the search form of `common\models\Moderator`.
 */
class ModeratorSearch extends Moderator
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tg_chat_id', 'tg_id', 'created_at', 'updated_at'], 'integer'],
            [['auth_token', 'eth_addr', 'tg_username', 'tg_first_name', 'tg_last_name', 'phone', 'current_task'], 'safe'],
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
        $query = Moderator::find();

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
            'tg_chat_id' => $this->tg_chat_id,
            'tg_id' => $this->tg_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'auth_token', $this->auth_token])
            ->andFilterWhere(['like', 'eth_addr', $this->eth_addr])
            ->andFilterWhere(['like', 'tg_username', $this->tg_username])
            ->andFilterWhere(['like', 'tg_first_name', $this->tg_first_name])
            ->andFilterWhere(['like', 'tg_last_name', $this->tg_last_name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'current_task', $this->current_task]);

        return $dataProvider;
    }
}
