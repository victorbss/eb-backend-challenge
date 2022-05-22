<?php

/**
 *  class ApiException
 *
 *  Classe responsável por gerenciar as exceptions de dados inválidos na requisição
 *
 *  @package Exceptions
 *  @author Victor Pereira
 */

namespace App\Exceptions;

use \Exception;
use \App\Http\Response\JSONResponse;

class ApiException extends Exception{

    /**
     * Detalhes do problema
     * @var array
     */
    protected $details = [];

    /**
     * Define a instancia da exception
     * @method __construct
     * @param  mixed       $message
     * @param  integer     $code
     */
    public function __construct($message, $code = 400){
      if(is_array($message)){
        $this->details = isset($message['details']) ? $message['details'] : [];
        $message = isset($message['message']) ? $message['message'] : 'Erro desconhecido';
      }
      parent::__construct($message, $code);
    }

    /**
     * Response
     * @method __toString
     * @return string
     */
    public function __toString(){
        return (new JSONResponse($this->getResponseMessage(), $this->getCode()))->getContent();
    }

    /**
     * Mensagem de retorno
     * @method getResponseMessage
     * @return array
     */
    public function getResponseMessage(){
      $return = [
        'error' => parent::getMessage()
      ];

      if(!empty($this->details)){
        $return['details'] = $this->details;
      }

      return $return;
    }
}
