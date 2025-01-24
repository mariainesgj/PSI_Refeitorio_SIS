<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "linhascarrinhos".
 *
 * @property int $id
 * @property int $prato_id
 * @property int ementa_id
 * @property int $carrinho_id
 *
 * @property Carrinho $carrinho
 */
class Linhascarrinho extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'linhascarrinhos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ementa_id', 'prato_id', 'carrinho_id'], 'required'],
            [['ementa_id', 'prato_id', 'carrinho_id'], 'integer'],
            [['carrinho_id'], 'exist', 'skipOnError' => true, 'targetClass' => Carrinho::class, 'targetAttribute' => ['carrinho_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ementa_id' => 'Ementa ID',
            'prato_id' => 'Prato ID',
            'carrinho_id' => 'Carrinho ID',
        ];
    }

    /**
     * Gets query for [[Carrinho]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarrinho()
    {
        return $this->hasOne(Carrinho::class, ['id' => 'carrinho_id']);
    }
}
