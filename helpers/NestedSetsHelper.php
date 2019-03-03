<?php

namespace app\helpers;

/**
 * Description of ViewHelper
 *
 * @author razikov
 */
class NestedSetsHelper
{
    /*
     * Генерирует массив элементов, либо возвращает false в случае провала.
     * @return false/array
     */

    public function generate($n = 5)
    {
        // Нельзя создать дерево с количеством элементов меньше 1
        if ($n <= 0) {
            return false;
        }
        // Создать корневой узел дерева
        $root = [
            'id' => 1,
            'name' => 1,
            'lft' => 1,
            'rgt' => $n * 2,
            'lvl' => 0,
        ];
        $id = $root['id'];
        $result = [$root];
        // Создать очередь узлов, для которых необходимо сгенерировать потомков
        if ($n === 1) {
            return $result;
        } else {
            $queue = [$root];
        }

        while (!empty($queue)) {
            $elem = array_shift($queue);
            $n = $elem['rgt'] - 1;
            $id++;
            $lft = $elem['lft'] + 1;
            $rgt = $elem['rgt'] - 1;
            $lvl = $elem['lvl'];
            // Создать потомков узла $elem
            while ($lft < $n) {
                $nrgt = rand($lft + 1, $rgt);
                // Если правый и левый ключ не делятся на 2 без остатка,
                // будет невозможно корректно создать потомков
                if (($nrgt - $lft) % 2 == 0) {
                    $nrgt++;
                }
                //Добавить узел в результат
                $node = [
                    'id' => $id,
                    'name' => $id,
                    'lft' => $lft,
                    'rgt' => $nrgt,
                    'lvl' => $lvl + 1
                ];
                $result[] = $node;
                //если узел может имеет потомков добавить его в очередь
                if (($nrgt - $lft) > 1) {
                    $queue[] = $node;
                }
                //если узел последний закончить генерацию потомков,
                if ($nrgt === $n) {
                    break;
                    //иначе подготовить данные для следующего узла
                } else {
                    $id++;
                    $lft = $nrgt + 1;
                }
            }
        }

        return $result;
    }

    public static function renderNestedSetTree($items)
    {
        $tree = '<div id="jstree"><ul>';
        for ($i = 0; $i < count($items); $i++) {
            // Вывести элемент
            $tree .= '<li data-jstree=\'{"opened":true}\'>' . $items[$i]->name;
            // Уровень текущего элемента
            $current = $items[$i]->lvl;
            // Уровень следующего элемента
            $next = isset($items[$i + 1]) ? $items[$i + 1]->lvl : 0;
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

}
