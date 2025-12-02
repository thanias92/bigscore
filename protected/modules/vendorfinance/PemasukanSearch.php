<?php

namespace app\modules\vendorfinance;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pemasukan;

/**
 * PemasukanSearch represents the model behind the search form of `app\models\Pemasukan`.
 */
class PemasukanSearch extends Pemasukan
{
    /**
     * {@inheritdoc}
     */
    public $queryString;
    public $status;
    public $tipe_pembayaran;
    public $purchase_type;
    public $tanggal_dari;
    public $tanggal_sampai;
    public function rules()
    {
        return [
            [['pemasukan_id', 'deals_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['purchase_date', 'description', 'pengirim_nama', 'pengirim_email', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['status', 'tipe_pembayaran', 'purchase_type', 'tanggal_dari', 'tanggal_sampai'], 'safe'],
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
        $query = Pemasukan::find()
            ->joinWith(['deals.customer', 'accountkeluar', 'penerimaanPembayarans']) // ← joinWith deals agar bisa akses kolomnya
            ->where(['pemasukan.deleted_at' => null]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'pemasukan.pemasukan_id' => $this->pemasukan_id,
            'pemasukan.deals_id' => $this->deals_id,
            'pemasukan.purchase_date' => $this->purchase_date,
            'pemasukan.created_by' => $this->created_by,
            'pemasukan.updated_by' => $this->updated_by,
            'pemasukan.deleted_by' => $this->deleted_by,
            'pemasukan.created_at' => $this->created_at,
            'pemasukan.updated_at' => $this->updated_at,
            'pemasukan.deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['ilike', 'pemasukan.pengirim_nama', $this->pengirim_nama])
            ->andFilterWhere(['ilike', 'pemasukan.pengirim_email', $this->pengirim_email])
            ->andFilterWhere(['ilike', 'pemasukan.description', $this->description]);

        // Search query gabungan
        if (!empty($this->queryString)) {
            $query->andWhere([
                'or',
                ['ilike', 'pemasukan.status', $this->queryString],
                ['ilike', 'pemasukan.tipe_pembayaran', $this->queryString],
                ['ilike', 'deals.purchase_type', $this->queryString], // ← ini sudah aman sekarang
                ['ilike', 'pemasukan.description', $this->queryString],
                ['ilike', 'pemasukan.pengirim_nama', $this->queryString],
                ['ilike', 'pemasukan.pengirim_email', $this->queryString],
                ['ilike', 'pemasukan.no_faktur', $this->queryString],
                ['ilike', 'customer.customer_name', $this->queryString],
            ]);
         
        }

        // Filter tanggal
        if (!empty($this->tanggal_dari) && !empty($this->tanggal_sampai)) {
            $query->andWhere(['between', 'pemasukan.purchase_date', $this->tanggal_dari, $this->tanggal_sampai]);
        }

        return $dataProvider;
    }
}
