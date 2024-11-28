<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "linhascarrinhos".
 *
 * @property int $id
 * @property int $quantidade
 * @property int $senha_id
 * @property int $carrinho_id
 *
 * @property Carrinho $carrinho
 * @property Senha $senha
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
            [['quantidade', 'senha_id', 'carrinho_id'], 'required'],
            [['quantidade', 'senha_id', 'carrinho_id'], 'integer'],
            [['carrinho_id'], 'exist', 'skipOnError' => true, 'targetClass' => Carrinho::class, 'targetAttribute' => ['carrinho_id' => 'id']],
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
            'senha_id' => 'Senha ID',
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

    /**
     * Gets query for [[Senha]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSenha()
    {
        return $this->hasOne(Senha::class, ['id' => 'senha_id']);
    }
}
