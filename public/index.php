<?php

require __DIR__ . '/../vendor/autoload.php';

use Alura\Cursos\Controller\InterfaceControladorRequisicao;

$caminho = $_SERVER['PATH_INFO']; // será ou /listar-cursos ou /novo-curso, etc.
$rotas = require __DIR__ . '/../config/routes.php';

// Se não existe o $caminho nas $rotas, dá erro 404 
if(!array_key_exists($caminho, $rotas)) {
    http_response_code(404);
    exit();
}

session_start();

$ehRotaDeLogin = stripos($caminho, 'login'); // procura a palavra login em $caminho, se não existir redireciona pra login
if(!isset($_SESSION['logado']) && $ehRotaDeLogin === false) {
    header('Location: /login');
    exit();
}

$classeControladora = $rotas[$caminho]; // Pega em $rotas, por exemplo, o valor em /listar-cursos, que é o nome completo da classe em routes.php que no caso é ListarCursos::class
/** @var InterfaceControladorRequisicao $controlador */
$controlador = new $classeControladora(); // Como o $classe tem o nome da classe "ListarCursos::class", instanciamos a classe pela variável
$controlador->processaRequisicao();