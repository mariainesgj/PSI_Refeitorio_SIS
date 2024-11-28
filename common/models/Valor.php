<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "valores".
 *
 * @property int $id
 * @property float $valor
 * @property float $iva
 *
 * @property Senha[] $senhas
 */
class Valor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'valores';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['valor', 'iva'], 'required'],
            [['valor', 'iva'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'valor' => 'Valor',
            'iva' => 'Iva',
        ];
    }

    /**
     * Gets query for [[Senhas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSenhas()
    {
        return $this->hasMany(Senha::class, ['valor_id' => 'id']);
    }
}
