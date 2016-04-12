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

  public function executeGenerate(sfWebRequest $request)
  {
    $data = @$_POST['data'] ?: "";

    if ($request->hasParameter('data')) {
      $data = $request->getParameter('data');
    }


    $return = CCommand::run($data);

    $this->Variables = $return['variables'];
    $this->Code = $return['code'];
    $this->Errors = $return['errors'];

    //var_dump($request->hasParameter('data'));

    echo "<pre>";
    //var_dump($data);
    //$this->Code = 'Wygenerowany kod';
  }

  public function executeSaveToFile(sfWebRequest $request)
  {

    header('Content-Description: File Transfer');
    if($request->hasParameter('data'))
      file_put_contents('./uploads/savedFiles/saveFile.txt', $request->getParameter('data'));
    $fileContent = file_get_contents('./uploads/savedFiles/saveFile.txt');

    header('Cache-Control: public, must-revalidate, max-age=0');
    header('Pragma: public');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

    header('Content-Type: application/force-download');
    header('Content-Type: application/octet-stream', false);
    header('Content-Type: application/download', false);
    header('Content-Disposition: attachment; filename="saveFile.txt";');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . strlen($fileContent));

    echo $fileContent;

    //return sfView::NONE;

    return $this->renderText($fileContent);

  }
}
