<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ementas".
 *
 * @property int $id
 * @property string $data
 * @property int $prato_normal
 * @property int $prato_vegetariano
 * @property int $sopa
 * @property int $cozinha_id
 *
 * @property Cozinha $cozinha
 * @property Senha[] $senhas
 */
class Ementa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ementas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data', 'prato_normal', 'prato_vegetariano', 'sopa', 'cozinha_id'], 'required'],
            ['data', 'date', 'format' => 'php:Y-m-d'],
            [['prato_normal', 'prato_vegetariano', 'sopa', 'cozinha_id'], 'integer'],
            [['cozinha_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cozinha::class, 'targetAttribute' => ['cozinha_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data' => 'Data',
            'prato_normal' => 'Prato Normal',
            'prato_vegetariano' => 'Prato Vegetariano',
            'sopa' => 'Sopa',
            'cozinha_id' => 'Cozinha ID',
        ];
    }

    /**
     * Gets query for [[Cozinha]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCozinha()
    {
        return $this->hasOne(Cozinha::class, ['id' => 'cozinha_id']);
    }

    /**
     * Gets query for [[Senhas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSenhas()
    {
        return $this->hasMany(Senha::class, ['ementa_id' => 'id']);
    }

    public function getPratoNormal()
    {
        return $this->hasOne(Prato::class, ['id' => 'prato_normal']);
    }

    public function getPratoVegetariano()
    {
        return $this->hasOne(Prato::class, ['id' => 'prato_vegetariano']);
    }

    public function getSopa()
    {
        return $this->hasOne(Prato::class, ['id' => 'sopa']);
    }
}
