<?php

namespace app\modules\vendorfinance;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AccountKeluar;

/**
 * AccountKeluarSearch represents the model behind the search form of `app\models\AccountKeluar`.
 */
class AccountKeluarSearch extends AccountKeluar
{
    public $queryString;
    public function rules()
    {
        return [
            [['id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['code', 'akun', 'penggunaan', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            ['queryString', 'safe'],
        ];
    }

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
        $query = AccountKeluar::find()
            ->where(['parent_id' => null]) // hanya tampilkan parent
            ->andWhere(['deleted_at' => null]); // soft delete support

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['code' => SORT_ASC]],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['ilike', 'code', $this->code])
            ->andFilterWhere(['ilike', 'akun', $this->akun])
            ->andFilterWhere(['ilike', 'penggunaan', $this->penggunaan]);

        return $dataProvider;
    }
}
