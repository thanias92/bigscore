<?php

namespace app\modules\vendorfinance;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Staff;

/**
 * StaffSearch represents the model behind the search form of `app\models\Staff`.
 */
class StaffSearch extends Staff
{
    /**
     * {@inheritdoc}
     */
    public $queryString;
    public function rules()
    {
        return [
            [['id_staff', 'id_agama', 'id_pendidikan', 'id_pekerjaan', 'id_negara', 'id_provinsi', 'id_kabupaten', 'id_kecamatan', 'id_kelurahan_desa', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['nama_lengkap', 'jenis_identitas', 'no_identitas', 'jenis_kelamin', 'status_perkawinan', 'tanggal_lahir', 'tempat_lahir', 'nama_ibu_kandung', 'suku', 'pekerjaan_lain', 'no_hp', 'no_wa', 'no_hp_alternatif', 'alamat', 'rt', 'rw', 'no_pegawai', 'jenis_pegawai', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
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
        $query = Staff::find()->where(['deleted_at' => null]);
        $dataProvider = new \yii\data\ActiveDataProvider([
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
                ['ilike', 'nama_lengkap', $queryString],
                ['ilike', 'alamat', $queryString],
                ['ilike', 'jenis_pegawai', $queryString],
                ['ilike', new \yii\db\Expression('CAST("no_hp" AS TEXT)'), $queryString],
            ]);
        }
    
        return $dataProvider;
    }    
    
}
