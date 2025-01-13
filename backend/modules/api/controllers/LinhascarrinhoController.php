<?php

namespace backend\modules\api\controllers;

use common\models\Carrinho;
use common\models\Linhascarrinho;
use HttpException;
use Yii;
use yii\rest\ActiveController;

/**
 * Default controller for the `api` module
 */
class LinhascarrinhoController extends ActiveController
{
    public $modelClass = 'common\models\Linhascarrinho';
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @throws HttpException
     * @throws \yii\db\Exception
     * @throws \yii\web\HttpException
     */
    public function actionAdicionarItem()
    {
        $userId = Yii::$app->user->id;

        if (!$userId) {
            throw new HttpException(401, 'Usuário não autenticado');
        }

        $carrinho = Carrinho::find()
            ->where(['user_id' => $userId, 'status' => 'active'])
            ->one();

        if (!$carrinho) {
            $carrinhoController = new CarrinhoController('carrinho', Yii::$app);
            $carrinho = $carrinhoController->actionCreateCart();
            if (!$carrinho) {
                throw new HttpException(400, 'Erro ao criar carrinho');
            }
        } else{
            $carrinhoId = $carrinho->id;
        }

        $data = Yii::$app->request->post();

        if (empty($data['ementa_id']) || empty($data['prato_id'])) {
            return [
                'status' => 'error',
                'message' => 'A Ementa ID e o Prato ID são obrigatórios.',
                'carrinho_id' => $carrinho->id,
            ];
        }

        $linhaCarrinho = new LinhasCarrinho();
        $linhaCarrinho->carrinho_id = $carrinhoId;
        $linhaCarrinho->ementa_id = $data['ementa_id'];
        $linhaCarrinho->prato_id = $data['prato_id'];

        if ($linhaCarrinho->save()) {
            return [
                'status' => 'success',
                'message' => 'Item adicionado ao carrinho com sucesso.',
                'data' => $linhaCarrinho,
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Erro ao adicionar item ao carrinho.',
            'errors' => $linhaCarrinho->errors,
        ];
    }


    public function actionExcluirItem($id) // {base_url}/linhascarrinho/excluir-item/123
    {
        $userId = Yii::$app->user->id;
        if (!$userId) {
            throw new HttpException(401, 'Utilizador não autenticado');
        }

        $linhaCarrinho = LinhasCarrinho::findOne($id);
        if (!$linhaCarrinho) {
            throw new HttpException(404, 'Senha do carrinho não encontrado');
        }

        if ($linhaCarrinho->delete()) {
            return [
                'status' => 'success',
                'message' => 'Senha excluída com sucesso.',
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Erro ao excluir senha do carrinho.',
        ];
    }
}



