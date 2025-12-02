<?php

namespace app\modules\vendorfinance;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pengaturanakun;

/**
 * PengaturanakunSearch represents the model behind the search form of `app\models\Pengaturanakun`.
 */
class PengaturanakunSearch extends Pengaturanakun
{
    /**
     * {@inheritdoc}
     */
    public $queryString;
    public function rules()
    {
        return [
            [['pengaturanakun_id', 'pemasukan_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['logo', 'ttd', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            ['queryString', 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
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
        $query = Pengaturanakun::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'pengaturanakun_id' => $this->pengaturanakun_id,
            'pemasukan_id' => $this->pemasukan_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
        ]);

        $query->andFilterWhere(['ilike', 'logo', $this->logo])
            ->andFilterWhere(['ilike', 'ttd', $this->ttd]);

        return $dataProvider;
    }
}
