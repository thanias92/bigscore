<?php

namespace app\modules\task;

use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use app\models\Implementation;
use yii\db\Query;
//kanza push ulang untuk hosting


class ImplementationSearch extends Model
{
    public $queryString;
    public $customer_name;
    public $email;
    public $no_telp;
    public $kontak_pribadi;
    public $nama_produk;
    public $duration;
    public $status;

    public function rules()
    {
        return [
            [['customer_name', 'queryString', 'email', 'no_telp', 'kontak_pribadi', 'nama_produk', 'duration', 'status'], 'safe'],
        ];
    }

    public function search($params)
    {
        $this->load($params, '');
        // echo $this->nama_produk;
        $query = (new \yii\db\Query())
            ->select([
                'deals.deals_id',
                'customer.customer_name AS nama_pelanggan',
                'customer.customer_email as email',
                'customer.customer_phone as no_telp',
                'customer.pic_name as kontak_pribadi',
                'product.product_name as nama_produk',
                new \yii\db\Expression("
                COALESCE(NULLIF(impl.status, ''), 'In Progress') AS status
            ")
            ])
            ->from('contract')
            ->innerJoin('pemasukan', 'contract.invoice_id = pemasukan.pemasukan_id')
            ->innerJoin('deals', 'pemasukan.deals_id = deals.deals_id')
            ->innerJoin('customer', 'deals.customer_id = customer.customer_id')
            ->innerJoin('product', 'deals.product_id = product.id_produk')
            ->leftJoin(['impl' => new \yii\db\Expression('
            LATERAL (
                SELECT status
                FROM implementation
                WHERE implementation.deals_id = deals.deals_id
                AND implementation.deleted_at IS NULL
                ORDER BY implementation.id_implementasi DESC
                LIMIT 1
            )
        ')], 'true')
            ->orderBy(['pemasukan.pemasukan_id' => SORT_DESC]);


        if (!empty($this->customer_name)) {
            $query->andWhere(['ilike', 'customer.customer_name', $this->customer_name]);
        }
        if (!empty($this->email)) {
            $query->andWhere(['ilike', 'customer.customer_email', $this->email]);
        }
        if (!empty($this->no_telp)) {
            $query->andWhere(['ilike', 'customer.customer_phone', $this->no_telp]);
        }
        if (!empty($this->kontak_pribadi)) {
            $query->andWhere(['ilike', 'customer.pic_name', $this->kontak_pribadi]);
        }
        if (!empty($this->nama_produk)) {
            $query->andWhere(['ilike', 'product.product_name', $this->nama_produk]);
        }


        $data           = $query->all();
        $filteredData   = [];

        foreach ($data as $key => $value) {
            $detail = $this->search_implementasi($value['deals_id'], $params);

            $cekdone    = $this->cekStatus('Done', $value['deals_id']);
            $cekopen    = $this->cekStatus('Open', $value['deals_id']);
            $cekprogres = $this->cekStatus('In Progress', $value['deals_id']);

            if (count($cekdone) > 0) {
                if (count($cekopen) > 0 || count($cekprogres) > 0) {
                    $status = "In Progress";
                } else {
                    $status = "Done";
                }
            } elseif (count($cekprogres) > 0) {
                $status = "In Progress";
            } else {
                $status = "Open";
            }

            $duration = $status === "Done" ? $this->getDurasi($value['deals_id']) : "";

            if (!empty($this->status) && stripos($status, trim($this->status)) === false) {
                continue;
            }

            if (!empty($this->duration) && stripos($duration, trim($this->duration)) === false) {
                continue;
            }


            $filteredData[] = [
                'deals_id'        => $value['deals_id'],
                'nama_pelanggan'  => $value['nama_pelanggan'],
                'email'           => $value['email'],
                'no_telp'         => $value['no_telp'],
                'kontak_pribadi'  => $value['kontak_pribadi'],
                'nama_produk'     => $value['nama_produk'],
                'status'          => $status,
                'duration'        => $duration,
                'detail'          => $detail,
            ];
        }

        return new ArrayDataProvider([
            'allModels' => $filteredData,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['nama_pelanggan', 'email', 'no_telp', 'kontak_pribadi', 'nama_produk', 'duration', 'status'],
            ],
        ]);
    }

    public function search_implementasi($deals_id, $params)
    {
        $this->load($params, '');
        $query = (new Query())
            ->from('implementation')
            ->andWhere(['deleted_at' => null])
            ->andWhere(['=', 'deals_id', $deals_id])
            ->orderBy('id_implementasi', 'asc');
        $data = $query->all();
        foreach ($data as $key => $value) {
            $data[$key]['detail_implementasi'] = (new Query())->from('implementation_detail')->andWhere(['=', 'id_implementasi', $value['id_implementasi']])->orderBy('activity')->all();
        }

        return $data;
    }

    public function cekStatus($status, $deal)
    {
        return (new Query())
            ->from('implementation')
            ->where(['status' => $status])
            ->andWhere(['deals_id' => $deal])
            ->all();
    }

    public function getDurasi($deal)
    {
        $querystart = (new Query())
            ->select('*')
            ->from('implementation')
            ->innerJoin('implementation_detail', 'implementation.id_implementasi = implementation_detail.id_implementasi')
            ->where(['implementation.deals_id' => $deal])
            ->andWhere(['not', ['implementation_detail.start_date' => null]])
            ->orderBy(['implementation_detail.start_date' => SORT_ASC])
            ->limit(1)
            ->one();

        $queryselesai = (new Query())
            ->select('*')
            ->from('implementation')
            ->innerJoin('implementation_detail', 'implementation.id_implementasi = implementation_detail.id_implementasi')
            ->where(['implementation.deals_id' => $deal])
            ->andWhere(['not', ['implementation_detail.completion_date' => null]])
            ->orderBy(['implementation_detail.completion_date' => SORT_DESC])
            ->limit(1)
            ->one();

        $startDate = $querystart['start_date'] ?? 'N/A';
        $completionDate = $queryselesai['completion_date'] ?? 'N/A';

        if ($startDate === 'N/A' || $completionDate === 'N/A') {
            return $startDate . ' - ' . $completionDate . ' (Tanggal tidak lengkap)';
        }

        try {
            $start = new \DateTime($startDate);
            $end = new \DateTime($completionDate);
            $interval = $start->diff($end);

            // Build dynamic string
            $parts = [];
            if ($interval->y > 0) {
                $parts[] = $interval->y . ' tahun';
            }
            if ($interval->m > 0) {
                $parts[] = $interval->m . ' bulan';
            }
            if ($interval->d > 0) {
                $parts[] = $interval->d . ' hari';
            }

            $durasiString = implode(', ', $parts);
        } catch (\Exception $e) {
            $durasiString = 'Error menghitung';
        }

        // return $startDate . ' - ' . $completionDate . ' (' . $durasiString . ')';
        return $durasiString;
    }
}
