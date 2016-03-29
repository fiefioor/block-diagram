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
        $return = array(
            'code' => array(),
            'errors' => array()
        );

        $blocks = self::deserialize($json);
        self::setPredicats($blocks);
       /* foreach ($blocks as $block) {
            if ($block instanceof CEndpredicateShape) var_dump($block);
        }*/

        $return['errors'] = self::validate($blocks);

        if(! $return['errors']) {

            $return['code'] = self::fill($return['code'], $blocks);
        }

        //var_dump($code);
        return $return;

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

        foreach ($decodeJson['blocks'] as $block) {
            //var_dump($block);
            switch ($block->type) {
                case 'operand':
                    $tmp = new COperandShape();
                    break;
                case 'predicate':
                    $tmp = new CPredicateShape();
                    break;
                case 'endpredicate':
                    $tmp = new CEndpredicateShape();
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
        $code[] = "int main(){";

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

        $trueIds = array();
        $nextTrueId = null;
        $falseIds = array();
        $nextFalseId = null;

        foreach ($blocks as $block) {
            if ($block->getId() == $predicat->getId()) {
                $trueIds[] = $predicat->getTrueId();
                $nextTrueId = $predicat->getTrueId();
                $falseIds[] = $predicat->getFalseId();
                $nextFalseId = $predicat->getFalseId();
            }
        }

        for ($i = 0; $i < count($blocks); $i++) {

        }

    }

    private static function setPredicats($blocks)
    {

        $id = self::findFirstBlock($blocks);

        $ids = array(
            'predicat' => array(),
            'endpredicat' => array(),
            'checked' => array()
        );

        $counter = array(
            'predicats' => 0,
            'endpredicats' => 0
        );

        $i = 0;

        while ($id) {
            // $i++;

            // if($i > 100) break;

            //for ($i = 0; $i < count($blocks); $i++) {
            //var_dump(count($ids['endpredicat']));

            $ids['checked'][] = $id;
            if ($blocks[$id] instanceof CPredicateShape) {
                $counter['predicats'] += 1;
                $ids['predicat'][$id] = $id;

                //var_dump(in_array($blocks[$id]->getTrueId(), $ids['checked']));

                if (in_array($blocks[$id]->getTrueId(), $ids['checked'])) {
                    $id = $blocks[$id]->getFalseId();
                } else {
                    $id = $blocks[$id]->getTrueId();
                }
                continue;
            }
            if ($blocks[$id] instanceof CEndpredicateShape) {
                $counter['endpredicats'] += 1;
                $ids['endpredicat'][$id] = $id;

                $counts = array_count_values($ids['checked']);
                $count = $counts[$id];

                if ($counter['endpredicats'] != 0 && $counter['predicats'] >= $counter['endpredicats']) {


                    $last_predicat = array_pop($ids['predicat']);
                    $last_endpredicat = array_pop($ids['endpredicat']);

                    if ($last_predicat && $count < 2) {
                        //var_dump($last_predicat);
                        $blocks[$last_endpredicat]->setPredicateId($last_predicat);
                        $blocks[$last_predicat]->setEndPredicatId($last_endpredicat);
                        $id = $last_predicat;
                        continue;
                    }

                }
            }

            $id = $blocks[$id]->getNextId();
        }

        //var_dump($counter);

        return $blocks;
    }

    private static function fill($code, $blocks)
    {

        $insertedIds = array();

        $code = self::preFill($code);

        $ids = array_keys($blocks);
        $nextId = null;

        foreach ($ids as $id) {
            if ($blocks[$id]->getPrevIds() == null) {
                $code = $blocks[$id]->fill($code);
                $nextId = $blocks[$id]->getNextId();
                $insertedIds[] = $id;
            }
        }

        while ($nextId) {
            //for ($i = 0; $i < count($blocks) - 1; $i++) {

            if ($nextId) {
                if (!$blocks[$nextId] instanceof CPredicateShape) {

                    $insertedIds[] = $blocks[$nextId]->getId();
                    if ($blocks[$nextId] instanceof CEndpredicateShape) {
                        $blocks[$nextId]->addVisit();
                        if ($blocks[$nextId]->CanMoveFoward()) {

                            if(!self::isLoop($blocks[$blocks[$nextId]->getPredicateId()]->getTrueId(),$blocks)){
                                $code = $blocks[$nextId]->fill($code);
                            }

                            $nextId = $blocks[$nextId]->getNextId();
                        } else {
                            $nextId = $blocks[$nextId]->getPredicateId();
                        }
                    } else {
                        $code = $blocks[$nextId]->fill($code);
                        $nextId = $blocks[$nextId]->getNextId();
                    }

                } else {
                    //$code = $blocks[$nextId]->fill($code);
                    $insertedIds[] = $blocks[$nextId]->getId();

                    if (array_search($blocks[$nextId]->getTrueId(), $insertedIds)) {

                        if(self::isLoop($blocks[$nextId]->getTrueId(), $blocks)){
                            $code = $blocks[$nextId]->fillEndWhile($code);
                        }else{
                            $code = $blocks[$nextId]->fillFalse($code);
                        }

                        //$code = $blocks[$nextId]->fillFalse($code);
                        $nextId = $blocks[$nextId]->getFalseId();

                    } else {

                        if(self::isLoop($blocks[$nextId]->getTrueId(), $blocks)){
                            $code = $blocks[$nextId]->fillWhile($code);
                        }else{
                            $code = $blocks[$nextId]->fillTrue($code);
                        }
                        $nextId = $blocks[$nextId]->getTrueId();

                    }


                }

            }
        }
        //var_dump($insertedIds);
        $code[] = self::postFill();

        return $code;
    }

    public static function findFirstBlock($blocks, $return_id = true)
    {
        $ids = array_keys($blocks);
        foreach ($ids as $id) {
            if ($blocks[$id]->getPrevIds() == null) {
                if ($return_id) {
                    return $id;
                } else {
                    return $blocks[$id];
                }
            }
        }
    }

    public static function validate($blocks)
    {
        $counter = array(
            'start' => 0,
            'stop' => 0,
            'predicat' => 0,
            'endpredicat' => 0
        );

        $errors = array();

        /** @var BaseShape $block */
        foreach ($blocks as $block) {
            if ($block->getPrevIds() == null)
                $counter['start'] += 1;

            if (!$block instanceof PredicateShape) {
                if ($block->getNextId() == null)
                    $counter['stop'] += 1;

                if ($block instanceof Endpredicate)
                    $counter['endpredicat'] += 1;

            } else {
                $counter['predicat'] += 1;

                if ($block->getTrueId() == null)
                    $errors[] = 'There is no true path for predicate block: ' . $block->getContent();

                if ($block->getFalseId() == null)
                    $errors[] = 'There is no false path for predicate block: ' . $block->getContent();

            }
        }

        if ($counter['start'] > 1)
            $errors[] = 'There is more than 1 start';
        else if ($counter['start'] == 0)
            $errors[] = 'There must be at least 1 start';


        if ($counter['stop'] > 1)
            $errors[] = 'There is more than 1 end';
        else if($counter['stop'] == 0)
            $errors[] = 'There must be at least 1 stop';

        if($counter['predicat'] != $counter['endpredicat'])
            $errors[] = 'Numbers of Predicats and Endpredicats are not Equal';

        return $errors;

    }


    /**
     * Funkcja sprawdzajaca czy dana sekwencja jest pętlą
     */
    public static function isLoop($startId, $blocks){


        $return = false;

        while(!$blocks[$startId] instanceof PredicateShape && !$blocks[$startId] instanceof Endpredicate && $startId){
            $startId = $blocks[$startId]->getNextId();
          //  var_dump($startId);

        }

        if($blocks[$startId] instanceof PredicateShape)
            $return = true;


        return $return;
    }

}