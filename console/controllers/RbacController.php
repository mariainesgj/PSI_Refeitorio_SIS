<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        $adminRole = $auth->createRole('administrador');
        $funcionarioRole = $auth->createRole('funcionario');
        $professorRole = $auth->createRole('professor');
        $alunoRole = $auth->createRole('aluno');

        $auth->add($adminRole);
        $auth->add($funcionarioRole);
        $auth->add($professorRole);
        $auth->add($alunoRole);

        // Criar as permissões
        $criarCozinhas = $auth->createPermission('criarCozinhas');
        $editarCozinhas = $auth->createPermission('editarCozinhas');
        $detalhesCozinhas = $auth->createPermission('verCozinhas');
        $eliminarCozinhas = $auth->createPermission('eliminarCozinhas');
        $criarPratos = $auth->createPermission('criarCozinhas');
        $editarCozinhas = $auth->createPermission('editarCozinhas');
        $detalhesCozinhas = $auth->createPermission('verCozinhas');
        $eliminarCozinhas = $auth->createPermission('eliminarCozinhas');
        $criarReservas = $auth->createPermission('criarReservas');
        $editarReservas = $auth->createPermission('editarReservas');
        $verReservas = $auth->createPermission('verReservas');
        $reservarOnline = $auth->createPermission('reservarOnline');
        $adicionarcarrinhoCompras = $auth->createPermission('adicionarcarrinhoCompras');
        $consultarFaturas = $auth->createPermission('consultarFaturas');
        $pagarReserva = $auth->createPermission('pagarReserva');
        $emitirFaturas = $auth->createPermission('emitirFaturas');
        $visualizarRelatorios = $auth->createPermission('visualizarRelatorios');
        $editarClientes = $auth->createPermission('editarClientes');
        $verClientes = $auth->createPermission('verClientes');
        $eliminarClientes = $auth->createPermission('eliminarClientes');


        // Adicionar as permissões ao authManager
        $auth->add($criarCozinhas);
        $auth->add($editarCozinhas);
        $auth->add($detalhesCozinhas);
        $auth->add($eliminarCozinhas);
        $auth->add($criarReservas);
        $auth->add($editarReservas);
        $auth->add($verReservas);
        $auth->add($reservarOnline);
        $auth->add($adicionarcarrinhoCompras);
        $auth->add($consultarFaturas);
        $auth->add($pagarReserva);
        $auth->add($emitirFaturas);
        $auth->add($visualizarRelatorios);
        $auth->add($editarClientes);
        $auth->add($verClientes);
        $auth->add($eliminarClientes);

        // Associar as permissões aos roles apropriados
        $auth->addChild($clienteRole, $reservarOnline);
        $auth->addChild($clienteRole, $adicionarcarrinhoCompras);
        $auth->addChild($clienteRole, $consultarFaturas);
        $auth->addChild($clienteRole, $classificarecomentarAlojamentos);
        $auth->addChild($clienteRole, $pagarReserva);

        $auth->addChild($funcionarioRole, $reservarPresencial);
        $auth->addChild($funcionarioRole, $criarReservas);
        $auth->addChild($funcionarioRole, $editarReservas);
        $auth->addChild($funcionarioRole, $verReservas);
        $auth->addChild($funcionarioRole, $eliminarReservas);
        $auth->addChild($funcionarioRole, $visualizarRelatorios);
        $auth->addChild($funcionarioRole, $criarClientes);
        $auth->addChild($funcionarioRole, $editarClientes);
        $auth->addChild($funcionarioRole, $verClientes);
        $auth->addChild($funcionarioRole, $eliminarClientes);
        $auth->addChild($funcionarioRole, $calcularValoresIva);

        $auth->addChild($fornecedorRole, $confirmarReserva);
        $auth->addChild($fornecedorRole, $criarAlojamentos);
        $auth->addChild($fornecedorRole, $editarAlojamentos);
        $auth->addChild($fornecedorRole, $eliminarAlojamentos);
        $auth->addChild($fornecedorRole, $detalhesAlojamentos);

        $auth->addChild($adminRole, $emitirFaturas);
        $auth->addChild($adminRole, $gerarRelatorios);
        $auth->addChild($adminRole, $criarAlojamentos);
        $auth->addChild($adminRole, $editarAlojamentos);
        $auth->addChild($adminRole, $detalhesAlojamentos);
        $auth->addChild($adminRole, $eliminarAlojamentos);
        $auth->addChild($adminRole, $criarReservas);
        $auth->addChild($adminRole, $editarReservas);
        $auth->addChild($adminRole, $verReservas);
        $auth->addChild($adminRole, $eliminarReservas);
        $auth->addChild($adminRole, $calcularValoresIva);

        echo "RBAC configuration completed.\n";
    }
}

