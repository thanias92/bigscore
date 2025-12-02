<?php

namespace app\modules\ticketing;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\NotificationPayment;

/**
 * NotificationPaymentSearch represents the model behind the search form of `app\models\NotificationPayment`.
 */
class NotificationPaymentSearch extends NotificationPayment
{
    public $queryString;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_notification_payment', 'id_pemasukan', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['status_payment_notification', 'date_notificatian', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['queryString'], 'safe'],
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
        $query = NotificationPayment::find()
            ->joinWith(['pemasukan.deals.customer', 'pemasukan.deals.product']) // join ke relasi customer & produk
            ->where(['notification_payment.deleted_at' => null]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id_notification_payment' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Filter spesifik kolom
        $query->andFilterWhere([
            'notification_payment.id_notification_payment' => $this->id_notification_payment,
            'notification_payment.id_pemasukan' => $this->id_pemasukan,
            'notification_payment.date_notificatian' => $this->date_notificatian,
            'notification_payment.created_by' => $this->created_by,
            'notification_payment.updated_by' => $this->updated_by,
            'notification_payment.deleted_by' => $this->deleted_by,
            'notification_payment.created_at' => $this->created_at,
            'notification_payment.updated_at' => $this->updated_at,
            'notification_payment.deleted_at' => $this->deleted_at,
        ]);

        // Filter partial match
        $query->andFilterWhere(['ilike', 'notification_payment.status_payment_notification', $this->status_payment_notification]);

        // Gabungan pencarian umum dari queryString
        if (!empty($this->queryString)) {
            $query->andWhere([
                'or',
                ['ilike', 'pemasukan.status', $this->queryString],
                ['ilike', 'customer.customer_name', $this->queryString],
                ['ilike', 'product.product_name', $this->queryString],
                // ['ilike', 'deals.purchase_type', $this->queryString],
            ]);
        }

        return $dataProvider;
    }
}
