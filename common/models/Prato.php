<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pratos".
 *
 * @property int $id
 * @property string $designacao
 * @property string $componentes
 * @property string|null $tipo
 *
 * @property Senha[] $senhas
 */
class Prato extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pratos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['designacao', 'componentes'], 'required'],
            [['tipo'], 'string'],
            [['designacao', 'componentes'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'designacao' => 'Designacao',
            'componentes' => 'Componentes',
            'tipo' => 'Tipo',
        ];
    }

    /**
     * Gets query for [[Senhas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSenhas()
    {
        return $this->hasMany(Senha::class, ['prato_id' => 'id']);
    }
}
