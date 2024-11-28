<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "movimentos".
 *
 * @property int $id
 * @property string|null $tipo
 * @property string $data
 * @property int $quantidade
 * @property int $origem
 * @property int $user_id
 *
 * @property User $user
 */
class Movimento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movimentos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo'], 'string'],
            [['data', 'quantidade', 'origem', 'user_id'], 'required'],
            [['data'], 'safe'],
            [['quantidade', 'origem', 'user_id'], 'integer'],
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
            'tipo' => 'Tipo',
            'data' => 'Data',
            'quantidade' => 'Quantidade',
            'origem' => 'Origem',
            'user_id' => 'User ID',
        ];
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

    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'user_id']);
    }
}
