<?php

/**
 * diagrams actions.
 *
 * @package    block-diagram
 * @subpackage diagrams
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class diagramsActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {

  }

  public function executeGenerate(sfWebRequest $request){
    //$data = @$_POST['data'] ?: "";

    var_dump($request->getParameter('data'));

    if($request->hasParameter('data')){
      $data = $request->getParameter('data');
    }

    CCommand::run($data);

    //var_dump($request->hasParameter('data'));

    echo "<pre>";
    //var_dump($data);
    $this->Code = 'Wygenerowany kod';
  }
}
