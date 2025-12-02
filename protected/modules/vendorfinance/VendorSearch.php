<?php

namespace app\modules\vendorfinance;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Vendor;

/**
 * VendorSearch represents the model behind the search form of `app\models\Vendor`.
 */
class VendorSearch extends Vendor
{
    /**
     * {@inheritdoc}
     */
    public $queryString;
    public function rules()
    {
        return [
            [['id_vendor', 'telp_vendor', 'telp_PIC', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['nama_vendor', 'alamat_vendor', 'email_vendor', 'nama_PIC', 'email_PIC', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            ['queryString', 'safe']
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Vendor::find()->where(['deleted_at' => null]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
    
        $this->load($params);
        $queryString = isset($params['queryString']) ? $params['queryString'] : '';
    
        if (!$this->validate()) {
            return $dataProvider;
        }
    
        if (!empty($queryString)) {
            $query->andFilterWhere([
                'or',
                ['ilike', 'nama_vendor', $queryString],
                ['ilike', 'alamat_vendor', $queryString],
                ['ilike', 'email_vendor', $queryString],
                ['ilike', new \yii\db\Expression('CAST("telp_vendor" AS TEXT)'), $queryString],
                ['ilike', 'nama_PIC', $queryString],
                ['ilike', 'email_PIC', $queryString],
                ['ilike', new \yii\db\Expression('CAST("telp_PIC" AS TEXT)'), $queryString],
            ]);
        }
    
        return $dataProvider;
    }
    
}
