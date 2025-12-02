<?php

namespace app\modules\vendorfinance;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pengeluaran;

/**
 * PengeluaranSearch represents the model behind the search form of `app\models\Pengeluaran`.
 */
class PengeluaranSearch extends Pengeluaran
{
    /**
     * {@inheritdoc}
     */
    public $queryString;
    public $nama_vendor;
    public $akun_pengeluaran;
    public function rules()
    {
        return [
            [['id_pengeluaran', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['tanggal', 'jumlah', 'jenis_pembayaran', 'id_vendor', 'keterangan', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['queryString'], 'safe'],
            [['nama_vendor'], 'safe']
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
        $query = Pengeluaran::find()->alias('p')
            ->joinWith(['vendor'])
            ->leftJoin(['a' => 'accountkeluar'], 'a.id = p.accountkeluar_id')
            ->where(['p.deleted_at' => null]);
    
        $this->load($params);
    
        // Logika pencarian real-time
        if (!empty($this->queryString)) {
            $query->andFilterWhere([
                'or',
                ['ilike', 'p.no_pengeluaran', $this->queryString],
                ['ilike', 'vendor.nama_vendor', $this->queryString],
                ['ilike', 'a.akun', $this->queryString],
                ['ilike', 'p.keterangan', $this->queryString],
            ]);
        }
    
        return new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
            'sort' => ['defaultOrder' => ['tanggal' => SORT_DESC]],
        ]);
    }
    
}
