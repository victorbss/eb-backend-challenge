<?php

/**
*
* Rotas da API
* @author Victor Pereira
*
*/

use \Illuminate\Http\Request;
use \App\Http\Response\JSONResponse;

use \App\Http\Controllers\Accounts as AccountController;

/**
* Recurso responsável por resetar estado dos dados utilizados na API
* @author Victor Pereira
*/
$router->post('/reset', function (Request $request) use ($router) {
    //RESETA REGISTRO DE CONTAS NO REDIS
    $redis = new Predis\Client();
    $redis->set('accounts', '{}');
    return (new JSONResponse('OK', 200));
});

/**
* Recurso responsável por consultar saldo em conta
* @author Victor Pereira
*/
$router->get('/balance', function (Request $request) use ($router) {
    //BUSCA REGISTRO DE CONTAS NO REDIS
    $redis = new Predis\Client();
    $accounts = json_decode($redis->get('accounts'), true);

    //QUERY PARAM
    $searchId = $request->get('account_id');

    //CONSULTA SALDO DE CONTA EXISTENTE
    foreach ($accounts as $account) {
        if($account['id'] == $searchId){
            return (new JSONResponse($account['balance'], 200));
        }
    }
    
    //RESPONSE PARA CONTA NÃO EXISTENTE
    return (new JSONResponse(0, 404));
});

/**
* Recurso responsável por executar ação em conta
* @author Victor Pereira
*/
$router->post('/event', function (Request $request) use ($router) {
    //REQUEST BODY
    $body = $request->JSON()->all();
    $statusCode = 201;

    //VERIFICA TIPO DE AÇÃO A SER EXECUTADA EM CONTA
    switch ($body['type']) {
        case 'deposit':
            $response = (new AccountController)->setRequest($request)
                                               ->deposit($body['destination'], $body['amount']);
            break;

        case 'withdraw':
            $response = (new AccountController)->setRequest($request)
                                               ->withdraw($body['origin'], $body['amount']);
            break;
    }

    //TRATA STATUS CODE PARA CONTA NÃO ENCONTRADA PARA WITHDRAW
    if($response == 0){
        $statusCode = 404;
    }
    
    //RESPONSE
    return (new JSONResponse($response, $statusCode));
});