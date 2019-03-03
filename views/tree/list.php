<?php

use app\helpers\NestedSetsHelper;

?>

<?= \yii\helpers\Html::tag('p', sprintf("Найдено %d элементов", count($items))); ?>
<?= NestedSetsHelper::renderNestedSetTree($items); ?>