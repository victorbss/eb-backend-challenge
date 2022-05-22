<?php

/**
 *  class JSONResponse
 *
 *  Classe responsável pelas respostas da API
 *
 *  @package Http
 *  @subpackage Response
 *  @author Victor Pereira
 */

namespace App\Http\Response;

class JSONResponse extends \Illuminate\Http\Response{

  /**
   * Define os dados do Response
   * @overwrite
   * @method __construct
   * @param  array       $body
   * @param  integer     $statusCode
   */
  public function __construct($body,$statusCode){
    parent::__construct(json_encode($body,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),$statusCode);
    $this->header('Content-Type', 'application/json');
  }

  /**
   * Método responsável por definir um array como conteudo
   * @method setContentWithAnArray
   * @param  array             $body
   */
  public function setContentWithAnArray($body){
    parent::setContent(json_encode($body,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
  }

  /**
   * Método resonsável por obter o conteudo em ARRAY
   * @method getArrayContents
   * @return array
   */
  public function getArrayContents(){
    return json_decode(parent::getContent(),true);
  }

}
