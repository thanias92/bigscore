<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "deal_quotations".
 * @property int $id
 * @property int $deal_id
 * @property int $quotation_id
 * @property int|null $is_active
 * @property string|null $created_at
 * @property Deals $deal
 * @property Quotation $quotation
 */

class DealQuotations extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'deal_quotations';
    }

    public function getDeal()
    {
        return $this->hasOne(Deals::class, ['deals_id' => 'deal_id']);
    }
    
    public function getQuotation()
    {
        return $this->hasOne(Quotation::class, ['quotation_id' => 'quotation_id']);
    }
}