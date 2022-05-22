<?php

 /**
 *  class ApiRequest
 *
 *  Clase responsável pelos comportamentos básicos de uma classe de API
 *
 *  @package Core
 *  @subpackage Api
 *  @author Victor Pereira
 */

namespace App\Core\Api;

use \App\Exceptions\ApiInvalidDataException;

class ApiRequest{

    /**
     * Request
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Método repsonsável por definir uma request
     * @method setRequest
     * @param  \Illuminate\Http\Request $request
     */
    public function setRequest($request){
      $this->request = $request;
      $this->request->filtrosAtivos = [];
      return $this;
    }

    /**
     * Método responsável por retornar a requisição
     *
     * @return \Illuminate\Http\Request
     */
    public function getRequest(){
        return $this->request;
    }

    /**
     * Método responsável por rejeitar uma requisição
     * @method rejeitarRequest
     * @param  array          $errors
     */
    public function rejeitarRequest($errors){
      $errors = array_filter($errors);
      throw new ApiInvalidDataException(['message'=>'Requisição rejeitada.','details'=>$errors], 400);
    }

}
