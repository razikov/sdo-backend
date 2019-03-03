<?php

namespace app\models\tree;

use Yii;
use yii\db\Query;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Теория есть тут http://www.getinfo.ru/article610.html
 * 
 */
class NestedSetTree
{
    const POS_FIRST_NODE = -1;
    const POS_LAST_NODE = -2;
    
    public $db;
    public $tableName = 'tree';
    public $keyTree = 'tree'; // for multiple tree
    public $keyId = 'id';
    public $keyParentId = 'parent_id';
    public $keyLft = 'lft';
    public $keyRgt = 'rgt';
    public $keyDepth = 'depth'; // aka level
    public $keyPath = 'path'; // aka level
    
    public $treeId = 1; // default value for $this->keyTree

    /**
     * 
     * @param array $config
     */
    public function __construct($config)
    {
        $this->db = \Yii::$app->get('ilias');
        $this->tableName = ArrayHelper::getValue($config, 'tableName', $this->tableName);
        $this->treeId = ArrayHelper::getValue($config, 'treeId', $this->treeId);
        $this->keyTree = ArrayHelper::getValue($config, 'keyTree', $this->keyTree);
        $this->keyId = ArrayHelper::getValue($config, 'keyId', $this->keyId);
        $this->keyParentId = ArrayHelper::getValue($config, 'keyParentId', $this->keyParentId);
        $this->keyLft = ArrayHelper::getValue($config, 'keyLft', $this->keyLft);
        $this->keyRgt = ArrayHelper::getValue($config, 'keyRgt', $this->keyRgt);
        $this->keyDepth = ArrayHelper::getValue($config, 'keyDepth', $this->keyDepth);
        $this->keyPath = ArrayHelper::getValue($config, 'keyPath', $this->keyPath);
    }

    public function getAll(int $treeId) : array
    {
        $data = (new Query())
                ->select(['*'])
                ->from($this->tableName)
                ->where([$this->keyTree => $treeId])
                ->orderBy($this->keyLft)
                ->all($this->db);

        return $data;
    }
    
    /**
     * Get subtree ids
     * @param int $nodeId
     * @return int[]
     */
    public function getSubTreeIds(int $nodeId) : array
    {
        $data = (new Query())
                ->select(["s.{$this->keyId}"])
                ->from("{$this->tableName} s, {$this->tableName} t")
                ->andWhere([
                    'and',
                    "s.{$this->keyLft} > t.{$this->keyLft}",
                    "s.{$this->keyRgt} < t.{$this->keyRgt}",
                    ["t.{$this->keyTree}" => $nodeId],
                ])
                ->column($this->db);

        return $data ?? [];
    }

    /**
     * Выбирает потомков по граничным ключам
     * 
     * @see $this->getSubTreeIds()
     * @param int $treeId
     * @param int $lft - левый индекс родителя
     * @param int $rgt - правый индекс родителя
     * @return array
     */
    public function getChilds(int $treeId, int $lft, int $rgt) : array
    {
        $data = (new Query())
                ->select(['*'])
                ->from("{$this->tableName} s")
                ->where([$this->keyTree => $treeId])
                ->andWhere(['>=', "s.{$this->keyLft}", $lft])
                ->andWhere(['<=', "s.{$this->keyRgt}", $rgt])
                ->orderBy("s.{$this->keyLft}")
                ->all($this->db);

        return $data ?? [];
    }
    
    /**
     * Выбирает предков родителя
     * 
     * @param int $treeId
     * @param int $lft - левый индекс родителя
     * @param int $rgt - правый индекс родителя
     * @return array
     */
    public function getParents(int $treeId, int $lft, int $rgt) : array
    {
        $data = (new Query())
                ->select(['*'])
                ->from("{$this->tableName} s")
                ->where(["s.{$this->keyTree}" => $treeId])
                ->andWhere(['<=', "s.{$this->keyLft}", $lft])
                ->andWhere(['>=', "s.{$this->keyRgt}", $rgt])
                ->orderBy("s.{$this->keyLft}")
                ->all($this->db);

        return $data ?? [];
    }
    
    /**
     * Выбор ветки в которой участвует наш узел-родитель
     * 
     * @param int $treeId
     * @param int $lft - левый индекс родителя
     * @param int $rgt - правый индекс родителя
     * @return array
     */
    public function getCurrent(int $treeId, int $lft, int $rgt) : array
    {
        $data = (new Query())
                ->select(['*'])
                ->from("{$this->tableName} s")
                ->where(["s.{$this->keyTree}" => $treeId])
                ->andWhere(['<', "s.{$this->keyLft}", $rgt])
                ->andWhere(['>', "s.{$this->keyRgt}", $lft])
                ->orderBy("s.{$this->keyLft}")
                ->all($this->db);

        return $data ?? [];
    }
    
    /**
     * Get path ids
     * @param int $endNodeId
     * @param int $startNodeId
     * @return int[]
     */
    public function getPathIds($endNodeId, $startNodeId = 0)
    {
        // This algorithms always does a full table space scan to retrieve the path
        // regardless whether indices on lft and rgt are set or not.
        // This algorithms performs well for small trees which are deeply nested.
        
        $data = (new Query())
                ->select(["t2.{$this->keyId}"])
                ->from("{$this->tableName} t1, {$this->tableName} t2")
                ->where(["t1.{$this->keyId}" => $endNodeId])
                ->andWhere(["t1.{$this->keyLft}" => new Expression("BETWEEN T2.{$this->keyLft} AND T2.{$this->keyRgt}")])
                ->andWhere(["t1.{$this->keyTree}", $this->treeId])
                ->andWhere(["t2.{$this->keyTree}", $this->treeId])
                ->orderBy("t2.{$this->keyDepth}")
                ->all($this->db);

        $takeId = $startNodeId == 0;
        foreach ($data as $row) {
            if ($takeId || $row[$this->keyId] == $startNodeId) {
                $takeId = true;
                $pathIds[] = $row[$this->keyId];
            }
        }
        return $pathIds ?? [];
    }

    /**
     * Insert tree node
     * @param int $nodeId
     * @param int $parentId
     * @param int $position // $node_id существующего потомка $parent_id или константы: POS_FIRST_NODE или POS_LAST_NODE
     *
     * @throws Exception
     */
    
    function InsertNode(int $nodeId, int $parentId, int $position)
    {
        /**
         * Если вставляется первым в родитея то индекс будет левый +1 от родителя
         * Если вставляется в указанный индекс то это должен быть правый индекс дочернего элемента родителя
         * Если вставляется последним то правый от родителя
         * Инициализация данных для новой ноды
         */
        switch ($position) {
            case self::POS_FIRST_NODE:
                $result = (new Query())
                        ->select('*')
                        ->from("{$this->tableName}")
                        ->where([
                            'and',
                            [$this->keyId => $parentId],
                            [$this->keyTree => $this->treeId],
                        ])
                        ->one($this->db);
                
                $left = $result[$this->keyLft];
                $lft = $left + 1;
                $rgt = $left + 2;
                $depth = $result[$this->keyDepth] + 1;
                break;
            case iTree::POS_LAST_NODE:
                $result = (new Query())
                        ->select('*')
                        ->from("{$this->tableName}")
                        ->where([
                            'and',
                            [$this->keyId => $parentId],
                            [$this->keyTree => $this->treeId],
                        ])
                        ->one($this->db);
                
                $right = $result[$this->keyRgt];
                $lft = $right;
                $rgt = $right + 1;
                $depth = $result[$this->keyDepth] + 1;
                break;
            default:
                $result = (new Query())
                        ->select('*')
                        ->from("{$this->tableName}")
                        ->where([
                            'and',
                            [$this->keyId => $position],
                            [$this->keyTree => $this->treeId],
                        ])
                        ->one($this->db);

                $right = $result[$this->keyRgt];
                $lft = $right + 1;
                $rgt = $right + 2;
                $depth = $result[$this->keyDepth];
                break;
        }
        
        /**
         * Обновление индексов
         */
        switch ($position) {
            case self::POS_FIRST_NODE:
                $this->db->createCommand()->update($this->tableName, [
                    $this->keyLft => new Expression("CASE WHEN {$this->keyLft} > :left THEN {$this->keyLft} + 2 ELSE {$this->keyLft} END", [
                        ':left' => $left,
                    ]),
                    $this->keyRgt => new Expression("CASE WHEN {$this->keyRgt} > :left THEN {$this->keyRgt} + 2 ELSE {$this->keyRgt} END", [
                        ':left' => $left,
                    ]),
                ], [
                    'and',
                    [$this->keyTree => $this->treeId],
                ])->execute();
                break;
            case self::POS_LAST_NODE:
                $this->db->createCommand()->update($this->tableName, [
                    $this->keyLft => new Expression("CASE WHEN {$this->keyLft} >= :right THEN {$this->keyLft} + 2 ELSE {$this->keyLft} END", [
                        ':right' => $right,
                    ]),
                    $this->keyRgt => new Expression("{$this->keyRgt} + 2"),
                ], [
                    'and',
                    ['>=', $this->keyRgt, $right],
                    [$this->keyTree => $this->treeId],
                ])->execute();
                break;
            default:
                $this->db->createCommand()->update($this->tableName, [
                    $this->keyLft => new Expression("CASE WHEN {$this->keyLft} > :rigth THEN {$this->keyLft} + 2 ELSE {$this->keyLft} END", [
                        ':rigth' => $right,
                    ]),
                    $this->keyRgt => new Expression("CASE WHEN {$this->keyRgt} > :rigth THEN {$this->keyRgt} + 2 ELSE {$this->keyRgt} END", [
                        ':rigth' => $right,
                    ]),
                ], [
                    'and',
                    [$this->keyTree => $this->treeId],
                ])->execute();
                break;
        }
        
        $this->db->createCommand()->insert($this->tableName, [
            $this->keyTree => $this->treeId,
            $this->keyId => $nodeId,
            $this->keyParentId => $parentId,
            $this->keyLft => $lft,
            $this->keyRgt => $rgt,
            $this->keyDepth => $depth,
        ])->execute();
        
    }
    
    /**
     * Delete a subtree
     * @param int $nodeId
     * @return bool
     */
    public function deleteTree($nodeId)
    {
        
        $node = (new Query())
                ->select('*')
                ->from("{$this->tableName}")
                ->where([
                    'and',
                    [$this->keyId => $nodeId],
                    [$this->keyTree => $this->treeId],
                ])
                ->one($this->db);
        
        $this->db->createCommand()->delete($this->tableName, [
            'and',
            ['>=', $this->keyLft, $node[$this->keyLft]],
            ['<=', $this->keyRgt, $node[$this->keyRgt]],
            [$this->keyTree => $node[$this->keyTree]], // очевидно что это будет $tree_id
        ])->execute();
        
        $diff = $node[$this->keyRgt] - $node[$this->keyLft] + 1;
        $this->db->createCommand()->update($this->tableName, [
            $this->keyLft => new Expression("CASE WHEN {$this->keyLft} > :lft THEN {$this->keyLft} - :diff ELSE {$this->keyLft} END", [
                ':lft' => $node[$this->keyLft],
                ':diff' => $diff,
            ]),
            $this->keyRgt => new Expression("{$this->keyRgt} - :diff", [':diff' => $diff]),
        ], [
            'and',
            ['>', $this->keyRgt, $node[$this->keyRgt]],
            [$this->keyTree => $node[$this->keyTree]],
        ])->execute();
        
    }
    
    /**
     * Move source subtree to target 
     * @param type $sourceId
     * @param type $targetId
     * @param type $position
     *
     * @throws Exception
     */
    public function moveTree($sourceId, $targetId, $position)
    {
        
        $result = (new Query())
                ->select('*')
                ->from("{$this->tableName}")
                ->where([
                    'and',
                    [
                        'or',
                        [$this->keyId => $sourceId],
                        [$this->keyId => $targetId],
                    ],
                    [$this->keyTree => $this->treeId],
                ])
                ->all($this->db);

        // Check in tree
        if (count($result) != 2) {
            throw new Exception('Error moving subtree');
        }
        foreach ($result as $row) {
            if ($row[$this->keyId] == $sourceId) {
                $sourceLft = $row[$this->keyLft];
                $sourceRgt = $row[$this->keyRgt];
                $sourceDepth = $row[$this->keyDepth];
                $sourceParent = $row[$this->keyParentId];
            } elseif ($row[$this->keyId] == $targetId) {
                $targetLft = $row[$this->keyLft];
                $targetRgt = $row[$this->keyRgt];
                $targetDepth = $row[$this->keyDepth];
            }
        }

        // Check target not child of source
        if ($targetLft >= $sourceLft and $targetRgt <= $sourceRgt) {
            throw new Exception('Error moving subtree: target is child of source');
        }
        
        if ($sourceParent != $targetId) {
            throw new Exception('Error moving subtree: target is child of source');
        }

        $spreadDiff = $sourceRgt - $sourceLft + 1;
        // Освобождаем место в дереве для перемещаемого узла + если есть его поддеревом
        $this->db->createCommand()->update($this->tableName, [
            $this->keyLft => new Expression("CASE WHEN {$this->keyLft} = :targetRgt THEN {$this->keyLft} + :spreadDiff ELSE {$this->keyLft} END", [
                ':targetRgt' => $targetRgt,
                ':spreadDiff' => $spreadDiff,
            ]),
            $this->keyRgt => new Expression("CASE WHEN {$this->keyRgt} >= :targetRgt THEN {$this->keyRgt} + :spreadDiff ELSE {$this->keyRgt} END", [
                ':targetRgt' => $targetRgt,
                ':spreadDiff' => $spreadDiff,
            ]),
        ], [
            [$this->keyTree => $this->treeId],
        ])->execute();
        
        if ($sourceLft > $targetRgt) { // перемещаемся вниз по дереву, перемещаемое поддерево сместилось на выделенный для него размер, 
            $whereOffset = $spreadDiff;
            $moveDiff = $targetRgt - $sourceLft - $spreadDiff;
        } else { 
            $whereOffset = 0;
            $moveDiff = $targetRgt - $sourceLft;
        }
        $depthDiff = $targetDepth - $sourceDepth + 1;

        $this->db->createCommand()->update($this->tableName, [
            $this->keyParentId => new Expression("CASE WHEN {$this->keyParentId} = :sourceParent THEN :targetId ELSE {$this->keyParentId} END", [
                ':sourceParent' => $sourceParent,
                ':targetId' => $targetRgt,
            ]),
            $this->keyRgt => new Expression("{$this->keyRgt} + :moveDiff", [
                ':moveDiff' => $moveDiff,
            ]),
            $this->keyLft => new Expression("{$this->keyLft} + :moveDiff", [
                ':moveDiff' => $moveDiff,
            ]),
            $this->keyDepth => new Expression("{$this->keyDepth} + :depthDiff", [
                ':depthDiff' => $depthDiff,
            ]),
        ], [
            ['>=', $this->keyLft, $sourceLft + $whereOffset],
            ['<=', $this->keyRgt, $sourceRgt + $whereOffset],
            [$this->keyTree => $this->treeId],
        ])->execute();
    }

    /**
     * Move to trash
     * @param int $nodeId
     */
    public function moveToTrash($nodeId)
    {
        $node = (new Query())
                ->select('*')
                ->from("{$this->tableName}")
                ->where([
                    'and',
                    [$this->keyTree => $this->treeId],
                    [$this->keyId => $nodeId],
                ])
                ->one($this->db);
                
        $this->db->createCommand()->update($this->tableName, [
            $this->keyTree => -1 * $node[$this->keyId],
        ], [
            "{$this->keyTree} = :pk",
            'lft BETWEEN :lft AND :rgt',
        ], [
            ':pk' => $node[$this->keyTree],
            ':lft' => $node[$this->keyLft],
            ':rgt' => $node[$this->keyRgt],
        ])->execute();
    }

}
