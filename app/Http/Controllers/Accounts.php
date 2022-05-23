<?php

/**                             
 *  class Accounts
 *
 *  Classe responsável por gerenciar os recursos da API de contas
 *
 *  @package Controllers
 *  @author Victor Pereira
 */

namespace App\Http\Controllers;

use Predis;

class Accounts extends \App\Core\Api\ApiRequest{

    /**
    * Método responsável por consultar saldo em conta
    */
    public function getBalance($accountId){
        //BUSCA REGISTRO DE CONTAS NO REDIS
        $redis = new Predis\Client();
        $accounts = json_decode($redis->get('accounts'), true);

        //CONSULTA SALDO DE CONTA EXISTENTE
        foreach ($accounts as $account) {
            if($account['id'] == $accountId){
                return $account['balance'];
            }
        }

        //RESPONSE PARA CONTA NÃO ENCONTRADA
        return 0;
    }

    /**
    * Método responsável por realizar depósito em conta ou cria nova conta com amount informado
    */
    public function deposit($accountId, $amount){
        //REDIS
        $redis = new Predis\Client();
        $accounts = json_decode($redis->get('accounts'), true);

        //VERIFICA SE CONTA EXISTE
        foreach ($accounts as $k => $account) {
            if($account['id'] == $accountId){
                //ADICIONA AMOUNT 
                $accounts[$k]['balance'] += $amount;
                $account = $accounts[$k];

                //ATUALIZA REDIS
                $jsonAccounts = json_encode($accounts, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $redis->set('accounts', $jsonAccounts);

                //RESPONSE
                return [
                    'destination' => $account
                ];
            }
        }

        //SE CONTA NÃO EXISTE CRIA NOVA CONTA COM AMOUNT INFORMADO
        return $this->newAccount($accountId, $amount);
    }

    /**
    * Método responsável por realizar transferência para outra conta
    */
    public function transfer($originAccountId, $destinationAccountId, $amount){
        //REDIS
        $redis = new Predis\Client();
        $accounts = json_decode($redis->get('accounts'), true);

        //VERIFICA SE CONTA EXISTE
        foreach ($accounts as $k => $account) {
            if($account['id'] == $originAccountId){
                //EXECUTA TRANSFERÊNCIA
                $withdraw = $this->withdraw($originAccountId, $amount);
                $deposit  = $this->deposit($destinationAccountId, $amount);

                //RESPONSE NO SCHEMA ESPERADO
                return [
                    'origin'      => [
                        'id'      => $withdraw['origin']['id'],
                        'balance' => $withdraw['origin']['balance']
                    ],
                    'destination' => [
                        'id'      => $deposit['destination']['id'],
                        'balance' => $deposit['destination']['balance']
                    ]
                ];
            }
        }

        //RESPONSE PARA CONTA NÃO ENCONTRADA
        return 0;
    }

    /**
    * Método responsável por criar nova conta
    */
    public function newAccount($accountId, $amount){
        //REDIS
        $redis = new Predis\Client();
        $accounts = json_decode($redis->get('accounts'), true);

        //MONTA ARRAY COM NOVA CONTA
        $account = [
            'id'      => $accountId,
            'balance' => (int)$amount
        ];
        
        //ADICIONA NOVA CONTA NO REDIS
        $accounts[] = $account;
        $jsonAccounts = json_encode($accounts, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $redis->set('accounts', $jsonAccounts);
        
        //RESPONSE
        return [
            'destination' => $account
        ];
    }

    /**
    * Método responsável por realizar saque em conta 
    */
    public function withdraw($accountId, $amount){
        //REDIS
        $redis = new Predis\Client();
        $accounts = json_decode($redis->get('accounts'), true);

        //VERIFICA SE CONTA EXISTE
        foreach ($accounts as $k => $account) {
            if($account['id'] == $accountId){
                //SUBTRAI AMOUNT 
                $balance = $accounts[$k]['balance'];
                $accounts[$k]['balance'] -= ($amount > $balance ? $balance : $amount);
                $account = $accounts[$k];

                //ATUALIZA REDIS
                $jsonAccounts = json_encode($accounts, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $redis->set('accounts', $jsonAccounts);

                //RESPONSE
                return [
                    'origin' => $account
                ];
            }
        }

        //RESPONSE PARA CONTA NÃO ENCONTRADA
        return 0;
    }

}
