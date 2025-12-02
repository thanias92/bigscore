<?php

use yii\db\Migration;

/**
 * Class m250707_163010_seed_accountkeluar
 */
class m250707_163010_seed_accountkeluar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /* ───────── 1. BUAT AKUN-AKUN INDUK ───────── */
        $parents = [
            //  code , akun                         , penggunaan
            ['4',      'Pendapatan'                 ,'pemasukan'],        // id 100
            ['5',      'Beban Pokok Pendapatan'     ,'pengeluaran'],      // id 200
            ['6',      'Beban Operasional'          ,'pengeluaran'],      // id 300
            ['7',      'Pendapatan (Beban Lain-Lain)','pemasukan'],       // id 400
            ['8',      'Beban Lain-Lain'            ,'pengeluaran'],      // id 500
        ];

        $id = 100;
        foreach ($parents as $p) {
            $this->insert('{{%accountkeluar}}', [
                'id'         => $id,
                'parent_id'  => null,
                'code'       => $p[0],
                'akun'       => $p[1],
                'penggunaan' => $p[2],
            ]);
            $parentMap[$p[0]] = $id;   // simpan id induk berdasar kode awal
            $id++;
        }

        /* ───────── 2. BUAT CHILD SESUAI LIST ─────── */
        $rows = [
            /* ============ PENDAPATAN (4-xxxx) ============ */
            ['4-40000','Pendapatan Jasa'],
            ['4-40001','Pendapatan Proyek'],
            ['4-40002','Pendapatan Emesys Clinic'],
            ['4-40003','Pendapatan SIMRS'],
            ['4-41000','Diskon Penjualan'],

            /* ============ BEBAN POKOK (5-xxxx) =========== */
            ['5-50000','Beban Pokok Pendapatan'],
            ['5-50201','Biaya Teknis'],
            ['5-50203','Biaya Marketing'],

            /* ============ BEBAN OPERASIONAL (6-xxxx) ===== */
            ['6-60000','Biaya Penjualan'],
            ['6-60001','Iklan & Promosi'],
            ['6-60002','Komunikasi - Penjualan'],
            ['6-60100','Biaya Umum & Administratif'],
            ['6-60101','Gaji'],
            ['6-60103','Lembur'],
            ['6-60104','Insentif'],
            ['6-60105','Bonus'],
            ['6-60106','THR'],
            ['6-60107','Donasi'],
            ['6-60201','Hiburan'],
            ['6-60202','Transportasi'],
            ['6-60203','Perbaikan & Pemeliharaan'],
            ['6-60204','Perjalanan Dinas - Umum'],
            ['6-60205','Makanan'],
            ['6-60209','Legal & Profesional'],
            ['6-60211','Sarana Kantor'],
            ['6-60212','Pelatihan & Pengembangan'],
            ['6-60214','Pajak dan Perizinan'],
            ['6-60217','Listrik'],
            ['6-60218','Internet'],
            ['6-60219','Langganan Software'],
            ['6-60301','Alat Tulis Kantor & Printing'],
            ['6-60302','Bea Materai'],
            ['6-60303','Keamanan dan Kebersihan'],
            ['6-60304','Persediaan Kantor'],
            ['6-60401','Biaya Sewa - Kendaraan'],
            ['6-60504','Penyusutan - Peralatan Kantor'],

            /* ============ PENDAPATAN LAIN-LAIN (7-xxxx) == */
            ['7-70000','Pendapatan Bunga - Bank'],
            ['7-70003','Rounding'],
            ['7-70009','Pendapatan Lain - lain'],

            /* ============ BEBAN LAIN-LAIN (8-xxxx) ======= */
            ['8-80099','Beban Lain - lain'],
        ];

        foreach ($rows as $r) {
            preg_match('/^(\d)/', $r[0], $m);        // ambil digit pertama (4/5/6/7/8)
            $rootCode = $m[1];                       // 4 / 5 / 6 / …
            $this->insert('{{%accountkeluar}}', [
                'parent_id'  => $parentMap[$rootCode],
                'code'       => $r[0],
                'akun'       => $r[1],
                'penggunaan' => in_array($rootCode, ['4','7']) ? 'pemasukan' : 'pengeluaran',
            ]);
        }

        /* Sinkronkan sequence PK jika memakai serial */
        $this->execute("SELECT setval(pg_get_serial_sequence('accountkeluar','id'),
                       (SELECT MAX(id) FROM accountkeluar));");
    }

    public function safeDown()
    {
        $this->delete('{{%accountkeluar}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250707_163010_seed_accountkeluar cannot be reverted.\n";

        return false;
    }
    */
}
