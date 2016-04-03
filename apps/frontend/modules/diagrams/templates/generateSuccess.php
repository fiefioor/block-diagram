<?php
/**
 * Created by PhpStorm.
 * User: fiefioor
 * Date: 05.03.16
 * Time: 13:44
 */

?>
<div class="content">

    <?php foreach($Errors as $error):

    echo "<p>".$error."<p>";

    endforeach; ?>

    <h1>Wygenerowany kod</h1>

    <?php foreach($Variables as $variable):
        //var_dump($line);
        echo "<p>".$variable."<p>";
    endforeach; ?>

    <?php foreach($Code as $line):
        //var_dump($line);
        echo "<p>".$line."<p>";
    endforeach; ?>
</div>