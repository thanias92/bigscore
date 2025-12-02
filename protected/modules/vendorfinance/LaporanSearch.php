<?php

namespace app\modules\vendorfinance;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Laporan;

/**
 * LaporanSearch represents the model behind the search form of `app\models\Laporan`.
 */
class LaporanSearch extends Laporan
{
    /**
     * {@inheritdoc}
     */
    public $queryString;
    public function rules()
    {
        return [
            [['laporan_id', 'pemasukan_id', 'pengeluaran_id', 'jumlah_pemasukan', 'jumlah_pengeluaran', 'saldo_akhir', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['tanggal', 'tipe_laporan', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
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
        $query = Laporan::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'laporan_id' => $this->laporan_id,
            'pemasukan_id' => $this->pemasukan_id,
            'pengeluaran_id' => $this->pengeluaran_id,
            'tanggal' => $this->tanggal,
            'jumlah_pemasukan' => $this->jumlah_pemasukan,
            'jumlah_pengeluaran' => $this->jumlah_pengeluaran,
            'saldo_akhir' => $this->saldo_akhir,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['ilike', 'tipe_laporan', $this->tipe_laporan]);

        return $dataProvider;
    }
}
