<?php

namespace common\models;

use common\models\Linhascarrinho;
use common\models\User;
use PDO;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "carrinhos".
 *
 * @property int $id
 * @property string $numero
 * @property float $subtotal
 * @property int $user_id
 * @property string $status
 *
 * @property Linhascarrinho[] $linhascarrinhos
 * @property User $user
 */
class Carrinho extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carrinhos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subtotal', 'user_id', 'status'], 'required'],
            [['subtotal'], 'number'],
            [['user_id'], 'integer'],
            [['numero'], 'string', 'max' => 50],
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
            'numero' => 'Numero',
            'subtotal' => 'Subtotal',
            'user_id' => 'User ID',
        ];
    }

    /**
     * Gets query for [[Linhascarrinhos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLinhascarrinhos()
    {
        return $this->hasMany(Linhascarrinho::class, ['carrinho_id' => 'id']);
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

    public static function findModel($id)
    {
        if (($model = Carrinho::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('O preçário solicitada não existe.');
    }

}
