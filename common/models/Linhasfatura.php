<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "linhasfaturas".
 *
 * @property int $id
 * @property int $quantidade
 * @property float $preco
 * @property float $taxa_iva
 * @property int $fatura_id
 * @property int $senha_id
 *
 * @property Fatura $fatura
 * @property Senha $senha
 */
class Linhasfatura extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'linhasfaturas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quantidade', 'preco', 'taxa_iva', 'fatura_id', 'senha_id'], 'required'],
            [['quantidade', 'fatura_id', 'senha_id'], 'integer'],
            [['preco', 'taxa_iva'], 'number'],
            [['fatura_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fatura::class, 'targetAttribute' => ['fatura_id' => 'id']],
            [['senha_id'], 'exist', 'skipOnError' => true, 'targetClass' => Senha::class, 'targetAttribute' => ['senha_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'quantidade' => 'Quantidade',
            'preco' => 'Preço',
            'taxa_iva' => 'Taxa de Iva',
            'fatura_id' => 'Fatura ID',
            'senha_id' => 'Senha ID',
        ];
    }

    /**
     * Gets query for [[Fatura]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFatura()
    {
        return $this->hasOne(Fatura::class, ['id' => 'fatura_id']);
    }

    /**
     * Gets query for [[Senha]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSenha()
    {
        return $this->hasOne(Senha::class, ['id' => 'senha_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (isset($this->taxa_iva)) {
                $this->taxa_iva = (float) str_replace('%', '', $this->taxa_iva);
            }

            return true;
        }
        return false;
    }
}
