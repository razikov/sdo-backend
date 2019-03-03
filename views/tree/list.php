<?php

use yii\helpers\ArrayHelper;

function renderNestedSetTree($items)
{
    $tree = '<div id="jstree"><ul>';
    for ($i = 0; $i < count($items); $i++) {
        // Вывести элемент
        $tree .= '<li data-jstree=\'{"opened":true}\'>' . ArrayHelper::getValue($items, [$i, 'child']);
        // Уровень текущего элемента
        $current = ArrayHelper::getValue($items, [$i, 'depth']);
        // Уровень следующего элемента
        $next = isset($items[$i + 1]) ? ArrayHelper::getValue($items, [$i+1, 'depth']) : 0;
        // Если следующий элемент является потомком, открыть список
        if ($next > $current) {
            $tree .= '<ul>';
        }
        // Если следующий элемент является предком
        if ($next < $current) {
            for ($j = 1; $j <= ($current - $next); $j++) {
                // Закрываем необходимое количество элементов и списков
                $tree .= '</li></ul>';
            }
        }
        // Если следующий элемент на том же уровне, закрываем текущий элемент
        if ($next == $current) {
            $tree .= '</li>';
        }
    }
    return $tree;
}
function renderMaterializePathTree($items)
{
    $tree = '<div id="jstree"><ul>';
    for ($i = 0; $i < count($items); $i++) {
        // Вывести элемент
        $tree .= '<li data-jstree=\'{"opened":true}\'>' . ArrayHelper::getValue($items, [$i, 'path']);
        // Уровень текущего элемента
        $current = ArrayHelper::getValue($items, [$i, 'depth']);
        // Уровень следующего элемента
        $next = isset($items[$i + 1]) ? ArrayHelper::getValue($items, [$i+1, 'depth']) : 0;
        // Если следующий элемент является потомком, открыть список
        if ($next > $current) {
            $tree .= '<ul>';
        }
        // Если следующий элемент является предком
        if ($next < $current) {
            for ($j = 1; $j <= ($current - $next); $j++) {
                // Закрываем необходимое количество элементов и списков
                $tree .= '</li></ul>';
            }
        }
        // Если следующий элемент на том же уровне, закрываем текущий элемент
        if ($next == $current) {
            $tree .= '</li>';
        }
    }
    return $tree;
}
?>

<?= \yii\helpers\Html::tag('p', sprintf("Найдено %d элементов", count($items))); ?>
<?php
if (Yii::$app->params['useTree'] == 'ns') {
    echo renderNestedSetTree($items);
} elseif (Yii::$app->params['useTree'] == 'mp') {
    echo renderMaterializePathTree($items);
}
?>