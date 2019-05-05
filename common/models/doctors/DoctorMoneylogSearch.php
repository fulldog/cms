<?php

namespace common\models\doctors;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\doctors\DoctorMoneylog;

/**
 * DoctorMoneylogSearch represents the model behind the search form about `common\models\doctors\DoctorMoneylog`.
 */
class DoctorMoneylogSearch extends DoctorMoneylog
{
    public $date;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'doctor_id', 'patient_id', 'status'], 'integer'],
            [['type', 'desc'], 'safe'],
            [['money'], 'number'],
            [['date'], 'string'],
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
        $query = DoctorMoneylog::find()->alias('a');
        $query->joinWith('relationPdmlog');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
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
            'doctor_id' => $this->doctor_id,
            'patient_id' => $this->patient_id,
            'money' => $this->money,
            'status' => $this->status,
        ]);
        $this->SearchAddHospitalId($query);
        $this->SearchAddTime($query, $params, __CLASS__);
        $query->andFilterWhere(['like', 'a.type', $this->type])
            ->andFilterWhere(['like', 'a.desc', $this->desc]);

        if ($this->date){
            $tmp = explode('~',str_replace(' ','',$this->date));
            $query->andFilterWhere(['between', 'pdm.date', $tmp[0], $tmp[1]]);
        }
        return $dataProvider;
    }
}
