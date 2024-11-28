<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "faturas".
 *
 * @property int $id
 * @property float $total_iliquido
 * @property float $total_iva
 * @property string $data
 * @property int $user_id
 * @property float $total_doc
 *
 * @property Linhasfatura[] $linhasfaturas
 * @property User $user
 */
class Fatura extends \yii\db\ActiveRecord
{
    public $street;
    public $locale;
    public $postalCode;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'faturas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['total_iliquido', 'total_iva', 'total_doc', 'data', 'user_id'], 'required'],
            [['total_iliquido', 'total_iva' ,'total_doc'], 'number'],
            [['data'], 'safe'],
            [['user_id'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'total_iliquido' => 'Total Ilíquido',
            'total_iva' => 'Total Iva',
            'data' => 'Data',
            'user_id' => 'User ID',
            'total_doc' => 'Total Documento',
        ];
    }

    /**
     * Gets query for [[Linhasfaturas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLinhasfaturas()
    {
        return $this->hasMany(Linhasfatura::class, ['fatura_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getUserProfile()
    {
        return $this->hasOne(Profile::class, ['id' => 'user_id']);
    }


}
