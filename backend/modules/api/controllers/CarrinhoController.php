<?php

namespace backend\modules\api\controllers;

use common\models\Carrinho;
use common\models\Valor;
use Yii;
use yii\rest\ActiveController;
use yii\web\HttpException;

/**
 * Default controller for the `api` module
 */
class CarrinhoController extends ActiveController
{
    public $modelClass = 'common\models\Carrinho';

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCreateCart()
    {
        $userId = Yii::$app->user->id;

        if (!$userId) {
            throw new HttpException(401, 'Usuário não autenticado');
        }

        $existingCart = Carrinho::find()
            ->where(['user_id' => $userId, 'status' => 'active'])
            ->one();

        if ($existingCart) {
            return [
                'status' => 'error',
                'message' => 'Já existe um carrinho ativo para este usuário.',
                'data' => $existingCart
            ];
        }

        $carrinhoCount = Carrinho::find()->count();

        $valor = Valor::findOne(1);
        if ($valor) {
            $subtotalAoCriar = $valor->valor;
        } else {
            throw new HttpException(404, 'Preço não encontrado');
        }

        $carrinho = new Carrinho();
        $carrinho->subtotal = (float) $subtotalAoCriar;
        $carrinho->user_id = $userId;
        $carrinho->status = 'active';
        $carrinho->created_at = time();
        $numero = 'CAR' . str_pad($carrinhoCount + 1, 4, '0', STR_PAD_LEFT);
        $carrinho->numero = $numero;

        if ($carrinho->save()) {
            return [
                'status' => 'success',
                'message' => 'Carrinho criado com sucesso.',
                'data' => $carrinho,
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Erro ao criar o carrinho.',
            'errors' => $carrinho->errors,
        ];
    }

    public function actionGetCartById($id)
    {
        $carrinho = Carrinho::findOne($id);
        if (!$carrinho) {
            throw new HttpException(404, 'Carrinho não encontrado');
        }
        return [
            'status' => 'success',
            'data' => $carrinho,
        ];
    }

}
