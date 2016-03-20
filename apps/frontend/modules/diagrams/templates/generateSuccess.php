<?php
/**
 * Created by PhpStorm.
 * User: fiefioor
 * Date: 05.03.16
 * Time: 13:44
 */

?>
<div class="content">
    <h1>Wygenerowany kod</h1>
    <?php foreach($Code as $line):
        //var_dump($line);
        echo "<p>".$line."<p>";
    endforeach; ?>
</div>