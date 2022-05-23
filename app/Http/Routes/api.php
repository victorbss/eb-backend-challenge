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
    //NGROK DONT serve HTML content, must sign up for an ngrok account and install authtoken. (ERR_NGROK_6022)
    return (new JSONResponse('OK', 200));
});

/**
* Recurso responsável por consultar saldo em conta
* @author Victor Pereira
*/
$router->get('/balance', function (Request $request) use ($router) {
    //QUERY PARAM
    $accountId = $request->get('account_id');
    $statusCode = 200;

    $response = (new AccountController)->setRequest($request)
                                       ->getBalance($accountId);
    
    //TRATA STATUS CODE PARA CONTA ORIGEM NÃO ENCONTRADA
    if(empty($response)){
        $statusCode = 404;
    }
    
    //RESPONSE
    return (new JSONResponse($response, $statusCode));
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
        
        case 'transfer':
            $response = (new AccountController)->setRequest($request)
                                                ->transfer($body['origin'], $body['destination'], $body['amount']);
            break;
    }

    //TRATA STATUS CODE PARA CONTA ORIGEM NÃO ENCONTRADA
    if(empty($response)){
        $statusCode = 404;
    }
    
    //RESPONSE
    return (new JSONResponse($response, $statusCode));
});