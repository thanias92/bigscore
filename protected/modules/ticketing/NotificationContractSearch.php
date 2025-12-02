<?php

namespace app\modules\ticketing;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\NotificationContract;

class NotificationContractSearch extends NotificationContract
{
    public $queryString;

    public function rules()
    {
        return [
            [['id_notification_contract', 'contract_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['status_contract_notification', 'date_notificatian_contract', 'description', 'created_at', 'updated_at', 'deleted_at', 'queryString'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = NotificationContract::find()
            ->joinWith(['contract.invoice.deals.customer', 'contract.invoice.deals.product']) // join relasi lengkap

            ->where(['notification_contract.deleted_at' => null]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id_notification_contract' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Filter kolom spesifik
        $query->andFilterWhere([
            'notification_contract.id_notification_contract' => $this->id_notification_contract,
            'notification_contract.contract_id' => $this->contract_id,
            'notification_contract.date_notificatian_contract' => $this->date_notificatian_contract,
            'notification_contract.created_by' => $this->created_by,
            'notification_contract.updated_by' => $this->updated_by,
            'notification_contract.deleted_by' => $this->deleted_by,
            'notification_contract.created_at' => $this->created_at,
            'notification_contract.updated_at' => $this->updated_at,
            'notification_contract.deleted_at' => $this->deleted_at,
        ]);

        // Filter partial match
        $query->andFilterWhere(['ilike', 'notification_contract.status_contract_notification', $this->status_contract_notification]);

        // Gabungan pencarian umum dari queryString
        if (!empty($this->queryString)) {
            $query->andWhere([
                'or',
                ['ilike', 'contract.status_contract', $this->queryString],
                ['ilike', 'customer.customer_name', $this->queryString],
                ['ilike', 'product.product_name', $this->queryString],
            ]);
        }

        return $dataProvider;
    }
}
