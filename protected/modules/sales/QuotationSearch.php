<?php

namespace app\modules\sales;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Quotation;

/**
 * QuotationSearch represents the model behind the search form of `app\models\Quotation`.
 */
class QuotationSearch extends Quotation
{
    /**
     * {@inheritdoc}
     */
    public $queryString; // Properti untuk menampung keyword pencarian global

    public function rules()
    {
        return [
            // Hapus validasi integer yang tidak diperlukan untuk search
            [['quotation_id', 'customer_id', 'product_id'], 'integer'],
            // Jadikan semua atribut 'safe' untuk pencarian
            [['quotation_code', 'quotation_status', 'created_date', 'queryString'], 'safe'],
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
        // Ganti Quotation::find() dengan query yang sudah di-join dan eager loading
        $query = Quotation::find()
            ->with('customer', 'product') // Eager loading untuk performa
            ->joinWith(['customer', 'product']); // Join untuk bisa filter/sort berdasarkan relasi

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'quotation_id',
                    'quotation_code',
                    'created_date',
                    'quotation_status',
                    'customer.customer_name', // Tambahkan ini agar bisa sort berdasarkan nama customer
                    'product.product_name',   // Tambahkan ini agar bisa sort berdasarkan nama produk
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // Hapus filter lama yang per-kolom, ganti dengan satu filter global
        // dari properti 'queryString'
        $query->andFilterWhere(['ilike', 'quotation.quotation_code', $this->queryString])
            ->orFilterWhere(['ilike', 'customer.customer_name', $this->queryString])
            ->orFilterWhere(['ilike', 'product.product_name', $this->queryString])
            ->orFilterWhere(['ilike', 'quotation.quotation_status', $this->queryString]);

        // Pastikan hanya data yang belum dihapus (soft delete) yang tampil
        $query->andWhere(['quotation.deleted_at' => null]);

        return $dataProvider;
    }
}
