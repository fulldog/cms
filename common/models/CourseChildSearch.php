<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CourseChild;

/**
 * CourseChildSearch represents the model behind the search form about `common\models\CourseChild`.
 */
class CourseChildSearch extends CourseChild implements \backend\models\search\SearchInterface
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'course_id', 'created_at', 'updated_at'], 'integer'],
            [['title', 'desc', 'thumb', 'video'], 'safe'],
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
    public function search(array $params = [], array $options = [])
    {
        $query = CourseChild::find();

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
//            'id' => $this->id,
            'course_id' => $this->course_id,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ]);

//        $query->andFilterWhere(['like', 'title', $this->title])
//            ->andFilterWhere(['like', 'desc', $this->desc])
//            ->andFilterWhere(['like', 'thumb', $this->thumb])
//            ->andFilterWhere(['like', 'video', $this->video]);

        return $dataProvider;
    }
}
