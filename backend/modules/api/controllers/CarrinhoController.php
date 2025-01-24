<?php

namespace backend\modules\api\controllers;

use common\models\Carrinho;
use common\models\Fatura;
use common\models\Linhascarrinho;
use common\models\Linhasfatura;
use common\models\Movimento;
use common\models\Senha;
use common\models\Valor;
use Yii;
use yii\db\Query;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `api` module
 */
class CarrinhoController extends ActiveController
{
    public $modelClass = 'common\models\Carrinho';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Adicionar autenticação via token
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        return $this->render('index');
    }


    public function  actionCarrinhoAtivo(){
        $userId = Yii::$app->user->id;

        $existingCart = Carrinho::find()
            ->where(['user_id' => $userId, 'status' => 'ativo'])
            ->one();

        if(isset($existingCart)){
            $cartId = $existingCart->id;


            $query = new Query();

            $linhas = $query
                ->select("lc.*, p.designacao as prato_nome ,DATE(m.data) as date ")
                ->from("linhascarrinhos lc")
                ->innerJoin("ementas m", "m.id = lc.ementa_id ")
                ->innerJoin("pratos p", "p.id = lc.prato_id ")
                ->where(['carrinho_id' => $cartId])
                ->all();


            $existingCart = $existingCart->attributes;
            $existingCart["linhas"] = $linhas;

            return [
                'status' => 'success',
                'message' => 'Carrinho e respetivas linhas encontradas',
                'data' => $existingCart
            ];
        }
        Yii::$app->response->statusCode = 404;
        return ["status" => "empty", 'message' => 'Nenhuma carrinho ativo encontrado' , "data" => [] ];
    }


    public function actionCreateCart()
    {
        $userId = Yii::$app->user->id;


        $existingCart = Carrinho::find()
            ->where(['user_id' => $userId, 'status' => 'ativo'])
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
        $carrinho->status = 'ativo';
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

    public function actionCheckout(){
        $userId = Yii::$app->user->id;

        $rawBody = Yii::$app->request->getRawBody();
        $data = json_decode($rawBody, true);

        $cardNumber = $data["cardNumber"];
        $expirationDate = $data['expirationDate'];
        $securityCode =  $data['securityCode'];
        $cardHolder = $data['cardHolder'];

        $maskedCardNumber = '**** **** **** ' . substr($cardNumber, -4);

        $maskedSecurityCode = '**' . substr($securityCode, -1);
        $carrinho = Carrinho::find()->where(["user_id" => $userId,'status' => 'ativo'])->one();

        $valorSys = Valor::findOne(1);
        if (!isset($valorSys)) {
            throw new HttpException(404, 'Preço não encontrado');
        }


        if (!$carrinho) {
            Yii::$app->response->statusCode = 404;
            return [
                'status' => 'error',
                'message' => 'Erro inesperado'
            ];
        }

        $query = new Query();

        $linhasCarrinho = $query
            ->select("lc.*, p.designacao as prato_nome ,DATE(m.data) as date ")
            ->from("linhascarrinhos lc")
            ->innerJoin("ementas m", "m.id = lc.ementa_id ")
            ->innerJoin("pratos p", "p.id = lc.prato_id ")
            ->where(['carrinho_id' => $carrinho->id])
            ->all();

        if (count($linhasCarrinho) == 0) {
            Yii::$app->response->statusCode = 404;
            return [
                'status' => 'error',
                'message' => 'Erro inesperado'
            ];
        }

        $fatura = new Fatura();
        $fatura->total_iliquido = 0;
        $fatura->total_iva = 0;
        $fatura->user_id = Yii::$app->user->id;
        $fatura->data = date('Y-m-d');
        $fatura->total_doc = 0;

        if (!$fatura->save()) {
            return $this->renderError("Erro ao criar a fatura.");
        }

        $valorIliquido = 0;
        $valorTotalIva = 0;
        $valorTotal = 0;



        foreach ($linhasCarrinho as $linha) {

            $linha = (object) $linha;

            $senha = new Senha();
            $senha->ementa_id = $linha->ementa_id;
            $senha->prato_id = $linha->prato_id;
            $senha->data = Yii::$app->formatter->asDate($linha->date, 'yyyy-MM-dd');
            $senha->user_id = Yii::$app->user->id;
            $senha->valor = $valorSys->valor;
            $senha->iva = $valorSys->iva;

            $senha->pago = 1;

            if ($senha->save()) {
                $valor = $senha->valor;
                $iva = $senha->iva;

                $linhaFatura = new Linhasfatura();
                $linhaFatura->quantidade = 1;
                $linhaFatura->preco = $valor;
                $linhaFatura->taxa_iva = $iva;
                $linhaFatura->fatura_id = $fatura->id;
                $linhaFatura->senha_id = $senha->id;
                if ($linhaFatura->save()) {
                    $valorTotal += $linhaFatura->preco;
                    $valorTotalIva += ($linhaFatura->preco * $linhaFatura->taxa_iva) / 100;
                    $valorIliquido += $linhaFatura->preco;
                }

            } else {
                Yii::$app->response->statusCode = 500;
                return [
                    'status' => 'error',
                    'message' => 'Erro inesperado'
                ];
            }
        }

        $quantidadeLinhasCarrinho = count($linhasCarrinho);

        $movimento = new Movimento();
        $movimento->tipo = 'credito';
        $movimento->data = date('Y-m-d H:i:s');
        $movimento->origem = $fatura->id;
        $movimento->quantidade = $quantidadeLinhasCarrinho;
        $movimento->user_id = Yii::$app->user->id;
        //var_dump($movimento);exit;
        if (!$movimento->save()) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 'error',
                'message' => 'Erro inesperado'
            ];
        }

        $fatura->total_iva = $valorTotalIva;
        $fatura->total_iliquido = $valorTotal;
        $fatura->total_doc = $valorTotalIva + $valorTotal;
        $fatura->numero_cartao = $maskedCardNumber;
        $fatura->validade = $expirationDate;
        $fatura->codigo_seguranca = $maskedSecurityCode;
        $fatura->titular = $cardHolder;
        $fatura->save();

        $carrinho->status = 'finalizado';
        $carrinho->subtotal = $valorTotal;
        if ($carrinho->save()) {
            return [
                "status" => "success",
                "message" => "Sucesso!",
                "data" => "{}"
            ];
        } else {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 'error',
                'message' => 'Erro inesperado'
            ];
        }


    }

}
