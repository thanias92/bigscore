<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
 * This is the model class for table "pemasukan".
 *
 * @property int $pemasukan_id
 * @property int $deals_id
 * @property int $accountkeluar_id
 * @property int $sub_total
 * @property int $diskon
 * @property int $grand_total
 * @property int $tipe_pembayaran
 * @property int $price_product
 * @property string $purchase_type
 * @property string $purchase_date
 * @property string $description
 * @property string $bukti_bayar_path
 * @property string|null $no_faktur
 * @property int|null $sisa_tagihan
 * @property string|null $status
 * @property string|null $tgl_jatuhtempo
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Deals $deals
 * @property Customer $customer
 */
class Pemasukan extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;

  public static function tableName()
  {
    return 'pemasukan';
  }

  public function rules()
  {
    return [
      [['deals_id', 'purchase_date'], 'required'],
      [['deals_id', 'accountkeluar_id', 'sisa_tagihan', 'sub_total', 'grand_total', 'cicilan', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
      [['diskon'], 'number'],
      [['tipe_pembayaran'], 'in', 'range' => ['transfer', 'cash']],
      [['purchase_date', 'tgl_jatuhtempo', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
      [['description', 'tipe_pembayaran', 'pengirim_nama', 'pengirim_email'], 'safe'],
      [['no_faktur', 'status', 'bukti_bayar_path', 'description'], 'string', 'max' => 255],
      [['bukti_bayar'], 'file', 'skipOnEmpty' => true, 'extensions' => 'jpg, jpeg, png, pdf'],
      [['no_faktur', 'purchase_type', 'sub_total', 'diskon', 'grand_total', 'pengirim_nama', 'pengirim_email'], 'safe'],
      ['no_faktur', 'unique', 'targetClass' => self::class, 'message' => 'Nomor faktur sudah digunakan.'],
      [
        'cicilan',
        'required',
        'when' => fn($model) => $model->purchase_type === 'Outright Purchase - Installments',
        'whenClient' => "function () {
            return $('#purchase-type').val() === 'Outright Purchase - Installments';
        }",
        'message' => 'Jumlah cicilan wajib diisi untuk tipe pembayaran cicilan.',
      ],
      [
        'cicilan',
        'integer',
        'min' => 2,
        'when' => fn($model) => $model->purchase_type === 'Outright Purchase - Installments',
        'whenClient' => "function () {
            return $('#purchase-type').val() === 'Outright Purchase - Installments';
        }",
      ],
      [
        'cicilan',
        'default',
        'value' => 0,
        'when' => fn($model) => $model->purchase_type !== 'Outright Purchase - Installments',
      ],
      ['cicilan', 'validateCicilan'], // validasi jumlah unit minimal 12
    ];
  }

  public function attributeLabels()
  {
    return [
      'pemasukan_id' => 'Pemasukan ID',
      'deals_id' => 'Recipient Name',
      'accountkeluar_id' => 'Income Account',
      'purchase_date' => 'Payment date',
      'description' => 'Deskripsi',
      'no_faktur' => 'Invoice No',
      'sisa_tagihan' => 'Rest of the bill',
      'sub_total'   => 'Sub-total',
      'diskon'      => 'Diskon',
      'tipe_pembayaran' => 'Payment Type',
      'grand_total' => 'Grand Total',
      'cicilan'     => 'Instalment',
      'status' => 'Status',
      'tgl_jatuhtempo' => 'Due date',
      'pengirim_nama' => 'Sender Name',
      'pengirim_email' => 'Sender Email',
      'bukti_bayar' => 'Upload Proof of Payment',
      'bukti_bayar_path' => 'Payment Proof Path',
      'created_by' => 'Dibuat Oleh',
      'updated_by' => 'Diubah Oleh',
      'deleted_by' => 'Dihapus Oleh',
      'created_at' => 'Dibuat Pada',
      'updated_at' => 'Diubah Pada',
      'deleted_at' => 'Dihapus Pada',
    ];
  }

  // Atribut tambahan (virtual)
  public $penerima_nama;
  public $penerima_email;
  public $purchase_type;
  public $produk;
  public $unit;
  public $price_product;
  public $bukti_bayar;
  public $jumlah_cicilan;
  public $cicilan_ke;
  public $pemasukan_id_parent;


  public function getDeals()
  {
    return $this->hasOne(Deals::class, ['deals_id' => 'deals_id']);
  }

  public function getAccountkeluar()
  {
    return $this->hasOne(Accountkeluar::class, ['id' => 'accountkeluar_id']);
  }

  public function init()
  {
    parent::init();
    if ($this->isNewRecord) {
      $this->status = 'Menunggu Pembayaran';
    }
  }

  public function getGrandTotalCalculated(): float
  {
    $sub  = (float) ($this->sub_total ?: 0);
    $disk = (float) ($this->diskon ?: 0);
    return $sub + ($sub * .11) - ($sub * $disk / 100);
  }
  public function getTotalPembayaran()
  {
    // cicilan
    if ($this->cicilan) {
      return array_sum(
        array_map(fn($c) => $c->status === 'Lunas' ? $c->nominal : 0, $this->cicilans)
      );
    }
    // sekali bayar
    return $this->status === 'Lunas' ? $this->getGrandTotal() : 0;
  }

  public function getSisaTagihan()
  {
    $totalTerbayar = $this->getJumlahTerbayar();
    $totalPotongan = PenerimaanPembayaran::find()
      ->where(['pemasukan_id' => $this->pemasukan_id])
      ->sum('COALESCE(potongan_pajak,0)');

    // ✅ Tagihan dianggap lunas jika uang masuk + potongan = grand_total
    $dibayarDenganPotongan = $totalTerbayar + $totalPotongan;

    return max(0, $this->grand_total - $dibayarDenganPotongan);
  }

  public static function generateNoFaktur()
  {
    $year = date('Y');
    $month = date('m');
    $prefix = "INV/{$year}/{$month}/";

    $lastPemasukan = self::find()
      ->where(['like', 'no_faktur', $prefix, false])
      ->andWhere(['deleted_at' => null])
      ->orderBy(['pemasukan_id' => SORT_DESC])
      ->one();

    if ($lastPemasukan && preg_match('/\/(\d{3})$/', $lastPemasukan->no_faktur, $matches)) {
      $newNumber = (int)$matches[1] + 1;
    } else {
      $newNumber = 1;
    }

    return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
  }

  public function updateStatusIfLate()
  {
    date_default_timezone_set('Asia/Jakarta'); // pastikan timezone sesuai

    if ($this->status === 'Menunggu Pembayaran' && $this->tgl_jatuhtempo) {
      $dueDate = strtotime($this->tgl_jatuhtempo);
      $today = strtotime(date('Y-m-d'));

      if ($dueDate < $today) {
        $this->status = 'Telat Bayar';
        if ($this->isAttributeChanged('status')) {
          $this->save(false);
        }
      }
    }
  }
  public function afterFind()
  {
    parent::afterFind();
    if ($this->status !== 'Lunas' && $this->tgl_jatuhtempo && strtotime($this->tgl_jatuhtempo) < time()) {
      $this->status = 'Telat Bayar';
    }
  }
  public function getStatusLabel()
  {
    if ($this->status === 'Lunas') {
      return 'Lunas';
    }

    $today = date('Y-m-d');

    if ($this->tgl_jatuhtempo && $today > $this->tgl_jatuhtempo) {
      return 'Telat Bayar';
    }

    return 'Menunggu Pembayaran';
  }

  public function getJumlahTerbayar()
  {
    $ids = [$this->pemasukan_id];

    // Jika ini parent → ambil semua child
    $childIds = self::find()
      ->select('pemasukan_id')
      ->where(['parent_id' => $this->pemasukan_id])
      ->column();

    if (!empty($childIds)) {
      $ids = array_merge($ids, $childIds);
    }

    return (float) PenerimaanPembayaran::find()
      ->where(['pemasukan_id' => $ids])
      ->sum('jumlah_terbayar - COALESCE(potongan_pajak,0)');
  }

  public function getPembayaran()
  {
    return $this->hasMany(PenerimaanPembayaran::class, ['pemasukan_id' => 'pemasukan_id']);
  }

  public static function data_pemasukan_all()
  {
    $pemasukan = Pemasukan::find()->all();
    $data_pemasukan = [];

    foreach ($pemasukan as $pemasukan) {
      $data_pemasukan[$pemasukan->pemasukan_id] = $pemasukan->customer_name;
    }

    return $data_pemasukan;
  }

  public function validateCicilan($attribute, $params)
  {
    // 0 = tanpa cicilan, lewati validasi
    if ((int)$this->$attribute === 0) {
      return;
    }

    // ambil `unit` yang sudah di‑cast ke int
    $qty = (int)$this->unit;

    if ($qty < 12) {
      $this->addError(
        'cicilan',
        'Cicilan hanya tersedia untuk pembelian minimal 12 unit.'
      );
    }
  }
  public function beforeSave($insert)
  {
    if (parent::beforeSave($insert)) {
      // Selalu cek ulang status sebelum disimpan
      if (!empty($this->tgl_jatuhtempo)) {
        $jatuhTempo = strtotime($this->tgl_jatuhtempo);
        $hariIni = strtotime(date('Y-m-d'));

        if ($jatuhTempo < $hariIni) {
          $this->status = 'Telat Bayar';
        } else {
          $this->status = 'Menunggu Pembayaran';
        }
      } else {
        $this->status = 'Menunggu Pembayaran';
      }

      return true;
    }
    return false;
  }


  public function afterSave($insert, $changedAttributes)
  {
    parent::afterSave($insert, $changedAttributes);

    if (!$insert) return; // hanya saat CREATE

    // buat cicilan jika memenuhi syarat
    if ($this->cicilan > 0 && $this->unit >= 12) {
      $baseDate = new \DateTime($this->purchase_date ?: 'now');

      for ($i = 1; $i <= $this->cicilan; $i++) {
        $due = clone $baseDate;
        $due->modify("+$i month");

        $cicilan = new PemasukanCicilan([
          'pemasukan_id' => $this->pemasukan_id,
          'ke' => $i,
          'jatuh_tempo' => $due->format('Y-m-d'),
          'nominal' => null,
          'status' => 'Menunggu',
        ]);
      }
    }

    // Simpan notifikasi selalu, baik cicilan atau langsung
    if ($this->parent_id === null) {
      $notif = new \app\models\NotificationPayment([
        'id_pemasukan' => $this->pemasukan_id,
        'status_payment_notification' => 'Menunggu Pembayaran',
        'date_notificatian' => date('Y-m-d'),
        'created_by' => Yii::$app->user->id ?? null,
        'updated_by' => Yii::$app->user->id ?? null,
      ]);


      if (!$notif->save()) {
        Yii::error("Gagal simpan notifikasi: " . json_encode($notif->errors), __METHOD__);
      }
    }
  }


  public function isFullyPaid()
  {
    return $this->getJumlahTerbayar() >= $this->grand_total;
  }

  public function getCicilanAnak()
  {
    return $this->hasMany(Pemasukan::class, ['parent_id' => 'pemasukan_id']);
  }

  public function getPemasukanParent()
  {
    return $this->hasOne(Pemasukan::class, ['pemasukan_id' => 'parent_id']);
  }

  public function getCicilanProgress()
  {
    $lunas = $this->getCicilan()->where(['status' => 'Lunas'])->count();
    return [
      'total' => $this->cicilan,
      'sudah_bayar' => $lunas,
      'sisa' => $this->cicilan - $lunas,
      'berikutnya' => $lunas + 1,         // angsuran yang harus dibayar berikutnya
    ];
  }

  public function getCicilans()
  {
    return $this->hasMany(PemasukanCicilan::class, ['pemasukan_id' => 'pemasukan_id'])
      ->orderBy('ke');
  }
  public function getCicilanPertama()
  {
    return $this->hasOne(PemasukanCicilan::class, ['pemasukan_id' => 'pemasukan_id'])
      ->andWhere(['ke' => 1])
      ->orderBy(['ke' => SORT_ASC]);
  }
  public function getCicilan()
  {
    return $this->hasOne(PemasukanCicilan::class, ['id' => 'cicilan_id']);
  }
  public function getCicilanList()
  {
    return $this->hasMany(self::class, ['pemasukan_id_parent' => 'id']);
  }

  public function getGrandTotal()
  {
    return $this->getGrandTotalCalculated();
  }

  public function getPenerimaanPembayarans()
  {
    return $this->hasMany(PenerimaanPembayaran::class, ['pemasukan_id' => 'pemasukan_id']);
  }

  public function isCicilan()
  {
    return !empty($this->cicilans);
  }

  public static function data_pemasukan_all_detail($filter = [])
  {
    $query = self::find()
      ->with(['deals.customer', 'accountkeluar', 'cicilans', 'penerimaanPembayarans']) // relasi tambahan
      ->where(['deleted_at' => null]);

    $pemasukans = $query->all();
    $list = [];

    foreach ($pemasukans as $item) {
      $cicilan_lunas = count(array_filter($item->cicilan, fn($c) => $c->status === 'Lunas'));
      $total_bayar = array_sum(array_map(fn($p) => $p->jumlah_terbayar, $item->penerimaanPembayarans));

      $list[$item->pemasukan_id] = [
        'status'            => $item->statusLabel,
        'tanggal'           => $item->purchase_date,
        'purchase_type'     => $item->purchase_type ?? '-',
        'tipe_pembayaran'   => $item->tipe_pembayaran ?? '-',
        'no_faktur'         => $item->no_faktur ?? '-',
        'tgl_jatuh_tempo'   => $item->tgl_jatuhtempo ?? '-',
        'akun_pemasukan'    => $item->accountkeluar->akun ?? '-',
        'nama_customer'     => $item->deals->customer->nama ?? '-',
        'grand_total'       => Yii::$app->formatter->asCurrency($item->grand_total),
        'total_pembayaran'  => Yii::$app->formatter->asCurrency($total_bayar),
        'jumlah_cicilan'    => count($item->cicilan),
        'cicilan_lunas'     => $cicilan_lunas,
        'sisa_tagihan'      => Yii::$app->formatter->asCurrency($item->sisaTagihan),
      ];
    }

    return $list;
  }
  public function getTotalBayarDariSemuaCicilan()
  {
    $total = 0;
    foreach ($this->cicilanAnak as $child) {
      foreach ($child->penerimaanPembayarans as $bayar) {
        $total += $bayar->jumlah_terbayar;
      }
    }
    return $total;
  }
}
