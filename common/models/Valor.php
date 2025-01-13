<?php

namespace common\models;

use Yii;
use yii\web\NotFoundHttpException;

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

    public static function findModel($id)
    {
        if (($model = Valor::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('O preçário solicitada não existe.');
    }
}
