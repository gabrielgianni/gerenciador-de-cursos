<?php

require __DIR__ . '/../vendor/autoload.php';

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

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

$psr17Factory = new Psr17Factory();

$creator = new ServerRequestCreator(
    $psr17Factory, // ServerRequestFactory
    $psr17Factory, // UriFactory
    $psr17Factory, // UploadedFileFactory
    $psr17Factory  // StreamFactory
);

$request = $creator->fromGlobals();

$classeControladora = $rotas[$caminho]; // Pega em $rotas, por exemplo, o valor em /listar-cursos, que é o nome completo da classe em routes.php que no caso é ListarCursos::class
/** @var ContainerInterface $container */
$container = require __DIR__ . '/../config/dependencies.php';
/** @var RequestHandleInterface $controlador */
$controlador = $container->get($classeControladora);
$resposta = $controlador->handle($request);

foreach ($resposta->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}

echo $resposta->getBody();