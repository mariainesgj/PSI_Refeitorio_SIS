<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cozinhas".
 *
 * @property int $id
 * @property string $responsavel
 * @property string $localizacao
 * @property string $designacao
 * @property string $telemovel
 *
 * @property Ementa[] $ementas
 */
class Cozinha extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cozinhas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['responsavel', 'localizacao', 'designacao', 'telemovel'], 'required'],
            [['responsavel', 'localizacao', 'designacao'], 'string', 'max' => 50],
            [['telemovel'], 'string', 'max' => 9],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'responsavel' => 'Responsavel',
            'localizacao' => 'Localizacao',
            'designacao' => 'Designacao',
            'telemovel' => 'Telemovel',
        ];
    }

    /**
     * Gets query for [[Ementas]].
     *
     * @return \yii\db\ActiveQuery
     */
    /*public function getEmentas()
    {
        return $this->hasMany(Ementas::class, ['cozinha_id' => 'id']);
    }*/
}
