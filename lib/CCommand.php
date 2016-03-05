<?php

/**
 * Created by PhpStorm.
 * User: fiefioor
 * Date: 05.03.16
 * Time: 16:12
 */
class CCommand
{
    public static function run($json)
    {
        $code = array();

        $blocks = self::deserialize($json);
        var_dump($blocks);

        $code = self::fill($code, $blocks);

        var_dump($code);

    }

    /* metoda konwertujaca bloki na ich klassowe odpowiedniki
    przykladowy JSON:
   string(799) "{
  "blocks": [
    {
      "id": 1,
      "type": "operand",
      "text": "block #1",
      "position": {
        "x": 100,
        "y": 55
      }
    },
    {
      "id": 2,
      "type": "predicate",
      "text": "block #2",
      "position": {
        "x": 106,
        "y": 165
      }
    },
    {
      "id": 3,
      "type": "predicate",
      "text": "block #3",
      "position": {
        "x": 128,
        "y": 276
      }
    },
    {
      "id": 4,
      "type": "output",
      "text": "",
      "position": {
        "x": 339,
        "y": 134
      }
    }
  ],
  "links": [
    {
      "a": {
        "block_id": 1,
        "type": "bottom"
      },
      "b": {
        "block_id": 2,
        "type": "top"
      }
    }
  ]
}"

    */
    private static function deserialize($json)
    {
        $decodeJson = (array) json_decode($json);
        $return = array();

        foreach($decodeJson['blocks'] as $block){
            //var_dump($block);
            switch ($block->type)     {
                case 'operand':
                    $tmp = new COperandShape();
                    break;
                case 'predicate':
                    echo "predicate";
                    break;
                case 'input':
                    $tmp = new CInputShape();
                    break;
                case 'output':
                    $tmp = new COutputShape();
                    break;
            }
            $tmp->setId($block->id);
            $tmp->setContent($block->text);
            $return[$block->id] = $tmp;
        }

        foreach($decodeJson['links'] as $link){
            //if(!$return[$link->a->block_id] instanceof CPredShape ){
                $return[$link->a->block_id]->setNextId($link->b->block_id);
                $return[$link->b->block_id]->addPrevId($link->a->block_id);
            //}

        }

        return $return;
    }

    private static function preFill(){
        return "#include <iostream.h>\n

int main()\n
{\n";
    }

    private static function postFill(){
        return  "return 1; }";

    }

    private static function fill($code , $blocks){

        $code[] = self::preFill();

        /*$fristID = array_keys($blocks)[0];
        for($i = 0; $i < count($blocks); $i++){

        }*/

        $code[] = self::postFill();

        return $code;
    }

}