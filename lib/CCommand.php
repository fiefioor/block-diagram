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
        foreach ($blocks as $block) {
            if ($block instanceof CPredicateShape)
                $blocks = self::predicatesOrders($blocks, $block);
        }

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
        $decodeJson = (array)json_decode($json);
        $return = array();

        var_dump($decodeJson);

        foreach ($decodeJson['blocks'] as $block) {

            switch ($block->type) {
                case 'operand':
                    $tmp = new COperandShape();
                    break;
                case 'predicate':
                    $tmp = new CPredicateShape();
                    break;
                case 'endpredicate':
                    $tmp = new CEndPredicate();
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


        foreach ($decodeJson['links'] as $link) {

            var_dump($link);
            if (!$return[$link->a->block_id] instanceof CPredicateShape) {
                $return[$link->a->block_id]->setNextId($link->b->block_id);

            } else {
                if ($link->a->type == "right") {
                    $return[$link->a->block_id]->addFalseId($link->b->block_id);
                } else {
                    $return[$link->a->block_id]->addTrueId($link->b->block_id);
                }
            }
            $return[$link->b->block_id]->addPrevId($link->a->block_id);
        }

        return $return;
    }

    private static function preFill($code)
    {
        $code[] = "#include &ltiostream.h&gt";
        $code[] = "int main() {";

        return $code;
    }

    private static function postFill()
    {
        return "return 1; }";

    }


    /**
     * Funkcja ogarniacja predykaty
     *
     * @param $blocks - tablica blokow
     */
    private static function predicatesOrders($blocks, CPredicateShape $predicat)
    {

        $trueBlocs = array();
        $nextTrueId = null;
        $falseBlocs = array();
        $nextFalseId = null;

        $counter = array(
            'predicats' => 1,
            'endpredicats' => 0
        );

        foreach ($blocks as $block) {
            if ($block->getId() == $predicat->getId()) {
                $nextTrueId = $predicat->getTrueId();
                $nextFalseId = $predicat->getFalseId();
            }
        }

        /** true path */
        for ($i = 0; $i < count($blocks); $i++) {
            $block = self::findBlockById($blocks, $nextTrueId);

            if ($block) {

                if (!$block instanceof CPredicateShape && !$block instanceof CEndPredicate) {
                    $trueBlocs[] = $block;
                    $nextTrueId = $block->getNextId();
                    //unset($blocks[array_search($block,$blocks)]);
                } else if ($block instanceof CEndPredicate) {
                    break;
                }
            }
        }

        /** false path */
        for ($i = 0; $i < count($blocks); $i++) {
            $block = self::findBlockById($blocks,$nextFalseId);

            if ($block) {

                if (!$block instanceof CPredicateShape && !$block instanceof CEndPredicate) {
                    $falseBlocs[] = $block;
                    $nextFalseId = $block->getNextId();
                    //unset($blocks[array_search($block,$blocks)]);
                } else if ($block instanceof CEndPredicate) {
                    break;
                }
            }
        }

        $predicat->setFalseBlocks($falseBlocs);
        $predicat->setTrueBlocks($trueBlocs);

        foreach ($blocks as $block) {
            if ($block->getId() == $predicat->getId()) {
                $block = $predicat;
            }
        }

        return $blocks;

    }

    private static function fill($code, $blocks)
    {

        $insertedIds = array();

        $code = self::preFill($code);

        $ids = array_keys($blocks);
        $nextId = null;


        /** znajdowanie elementu startowego */
        foreach ($ids as $id) {
            if ($blocks[$id]->getPrevIds() == null) {
                $code = $blocks[$id]->fill($code);
                $nextId = $blocks[$id]->getNextId();
                $insertedIds[] = $id;
            }
        }


        /** wlasciwe wypelnianie */
        for ($i = 0; $i < count($blocks) - 1; $i++) {

            if (!$blocks[$nextId] instanceof CPredicateShape) {
                $code = $blocks[$nextId]->fill($code);
                $insertedIds[] = $blocks[$nextId]->getId();
                $nextId = $blocks[$nextId]->getNextId();
            } else {
                $code = $blocks[$nextId]->fill($code);
            }

        }

        $code[] = self::postFill();

        return $code;
    }

    public function findLastEndPredicateBlock($blocks, $type)
    {


    }

    public static function findBlockById($blocks, $id)
    {

        foreach ($blocks as $block) {
            if ($block->getId() == $id) return $block;
        }

        return null;

    }

}