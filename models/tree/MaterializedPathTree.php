<?php

namespace app\models\tree;

use yii\db\Expression;

class MaterializedPathTree
{
    const MAX_POSSIBLE_DEPTH = 100;
    
    public $db;
    public $tableName;
    public $keyTree = 'tree'; // for multiple tree
    public $keyId = 'id';
    public $keyParentId = 'parent_id';
    public $keyLft = 'lft';
    public $keyRgt = 'rgt';
    public $keyDepth = 'depth'; // aka level
    public $keyPath = 'path';
    
    public $treeId = 1;

    public function __construct($config)
    {
        $this->db = Yii::$app->get('ilias');
        $this->tableName = ArrayHelper::getValue($config, 'tableName');
        $this->treeId = ArrayHelper::getValue($config, 'treeId');
        $this->keyTree = ArrayHelper::getValue($config, 'keyTree');
        $this->keyId = ArrayHelper::getValue($config, 'keyId');
        $this->keyParentId = ArrayHelper::getValue($config, 'keyParentId');
        $this->keyLft = ArrayHelper::getValue($config, 'keyLft');
        $this->keyRgt = ArrayHelper::getValue($config, 'keyRgt');
        $this->keyDepth = ArrayHelper::getValue($config, 'keyDepth');
        $this->keyPath = ArrayHelper::getValue($config, 'keyPath');
    }

    /**
     * Get subtree ids
     * @param int $nodeId
     * @return array
     */
    public function getSubTreeIds($nodeId)
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
                
        $data = (new Query())
                ->select("{$this->keyId}")
                ->from("{$this->tableName}")
                ->where([
                    'and',
                    ['between', $this->keyPath, $node[$this->keyPath], $node[$this->keyPath].'.Z'],
                    [$this->keyId => $nodeId],
                    [$this->keyTree => $this->treeId],
                ])
                ->all($this->db);
        
        foreach ($data as $row) {
            $childs[] = $row[$this->keyId];
        }
        return $childs ?? [];
    }

    /**
     * Get path ids
     * @param int $endNodeId
     * @param int $startNodeId
     * @return array
     */
    public function getPathIds($endNodeId, $startNodeId = 0)
    {
        
        $data = (new Query())
                ->select("{$this->keyPath}")
                ->from("{$this->tableName}")
                ->where([
                    'and',
                    [$this->keyId => $endNodeId],
                ])
                ->all($this->db);
                
        $path = null;
        foreach ($data as $row) {
            $path = $row[$this->keyPath];
        }

        $pathIds = explode('.', $path);

        if ($startNodeId != 0) {
            while (count($pathIds) > 0 && $pathIds[0] != $startNodeId) {
                array_shift($pathIds);
            }
        }
        return $pathIds;
    }

    /**
     * Insert new node under parent node
     * @param int $nodeId
     * @param int $parentId
     * @param int $position
     *
     * @throws Exception
     */
    public function insertNode($nodeId, $parentId, $position)
    {
        
        $node = (new Query())
                ->select("*")
                ->from("{$this->tableName}")
                ->where([
                    'and',
                    [$this->keyId => $parentId],
                    [$this->keyTree => $this->treeId],
                ])
                ->one($this->db);
        
        if ($node[$this->keyParentId] === NULL) {
            throw new Exception('Parent node not found in tree');
        }

        if ($node[$this->keyDepth] >= self::MAX_POSSIBLE_DEPTH) {
            throw new Exception('Maximum tree depth exceeded');
        }

        $parentPath = $node[$this->keyPath];
        $depth = $node[$this->keyDepth] + 1;
        $lft = 0;
        $rgt = 0;

        $this->db->createCommand()->insert($this->tableName, [
            $this->keyTree => $this->treeId,
            $this->keyId => $nodeId,
            $this->keyParentId => $parentId,
            $this->keyLft => $lft,
            $this->keyRgt => $rgt,
            $this->keyDepth => $depth,
            $this->keyPath => $parentPath . "." . $position,
        ])->execute();
        
    }

    /**
     * Delete a subtree
     * @param int $nodeId
     *
     * @return bool
     */
    public function deleteTree($nodeId)
    {
        $node = (new Query())
                ->select("*")
                ->from("{$this->tableName}")
                ->where([
                    'and',
                    [$this->keyId => $nodeId],
                    [$this->keyTree => $this->treeId],
                ])
                ->one($this->db);
                
        $this->db->createCommand()->delete($this->tableName, [
            'and',
            ['between', $this->keyPath, $node[$this->keyPath], $node[$this->keyPath] . '.Z'],
            [$this->keyTree => $this->treeId],
        ])->execute();
        
    }

    /**
     * Move subtree to trash
     * @param type $nodeId
     *
     */
    public function moveToTrash($nodeId)
    {
        $node = (new Query())
                ->select("*")
                ->from("{$this->tableName}")
                ->where([
                    'and',
                    [$this->keyId => $nodeId],
                    [$this->keyTree => $this->treeId],
                ])
                ->one($this->db);

        $this->db->createCommand()->update($this->tableName, [
            $this->keyTree => -$nodeId,
        ], [
            'and',
            [$this->keyTree => $node[$this->keyTree]],
            ['between', $this->keyPath, $node['path'], $node['path'] . '.Z'],
        ])->execute();
        
    }

    /**
     * move source subtree to target node
     * @param int $sourceId
     * @param int $targetId
     * @param int $position
     * @return bool
     *
     * @throws Exception
     */
    public function moveTree($sourceId, $targetId, $position)
    {
        $data = (new Query())
                ->select("*")
                ->from("{$this->tableName}")
                ->where([
                    'and',
                    ['in', $this->keyId => [$sourceId, $targetId]],
                    [$this->keyTree => $this->treeId],
                ])
                ->limit(2)
                ->all($this->db);
                
        // Check in tree
        if (count($data) != 2) {
            throw new Exception('Error moving subtree');
        }

        foreach ($data as $row) {
            if ($row[$this->keyId] == $sourceId) {
                $sourcePath = $row[$this->keyPath];
                $sourceDepth = $row[$this->keyDepth];
                $sourceParent = $row[$this->keyParentId];
            } else {
                $targetPath = $row[$this->keyPath];
                $targetDepth = $row[$this->keyDepth];
            }
        }

        if ($targetDepth >= $sourceDepth) {
            // We move nodes deeper into the tree. Therefore we need to
            // check whether we might exceed the maximal path length.
            // We use FOR UPDATE here, because we don't want anyone to
            // insert new nodes while we move the subtree.

            $row = (new Query())
                ->select("MAX(depth) maxDepth")
                ->from("{$this->tableName}")
                ->where([
                    'and',
                    ['between', $this->keyPath, $sourcePath, $sourcePath . '.Z'],
                    [$this->keyTree => $this->treeId],
                ])
                ->one($this->db);

            if ($row['maxDepth'] - $sourceDepth + $targetDepth + 1 > self::MAX_POSSIBLE_DEPTH) {
                throw new Exception('Maximum tree depth exceeded');
            }
        }
        // Check target not child of source
        if (substr($targetPath . '.', 0, strlen($sourcePath) . '.') == $sourcePath . '.') {
            throw new Exception('Error moving subtree: target is child of source');
        }
        
        $depthDiff = $targetDepth - $sourceDepth + 1;

        $this->db->createCommand()->update($this->tableName, [
            $this->keyParentId => new Expression("CASE WHEN {$this->keyParentId} = :sourcePparent THEN :targetId ELSE {$this->keyParentId} END", [
                ':sourceParent' => $sourceParent,
                ':targetId' => $targetId,
            ]),
            $this->keyPath => new Expression("CONCAT(:targetPath, SUBSTRING({$this->keyPath},:strpos))", [
                ':targetPath' => $targetPath,
                ':strpos' => strrpos('.' . $sourcePath, '.'),
            ]),
            $this->keyDepth => new Expression("{$this->keyDepth} + :depthDiff", [
                ':depthDiff' => $depthDiff,
            ]),
        ], [
            'and',
            ['between', $this->keyPath, $sourcePath, $sourcePath . '.Z'],
            [$this->keyTree => $this->treeId],
        ])->execute();
        
    }

    public static function createFromParentReleation()
    {
        $parent = (new Query())
                ->select("*")
                ->distinct()
                ->from("{$this->tableName}")
                ->where([
                    'and',
                    [$this->keyId => 0],
                ])
                ->one($this->db);
                
        if ($parent) {
            self::createMaterializedPath(0, '');
        }
    }

    /**
     * @param type $parent
     * @param type $parentPath
     * @return bool
     */
    private static function createMaterializedPath($parent, $parentPath)
    {
        
        $this->db->createCommand()->update($this->tableName, [
            $this->keyPath => new Expression("CONCAT(COALESCE(:parentPath, ''), COALESCE({$this->keyId}, ''))", [
                ':parentPath' => $parentPath,
            ]),
        ], [
            'and',
            [$this->keyParentId => $parent],
        ])->execute();
        
        $data = (new Query())
                ->select("*")
                ->from("{$this->tableName}")
                ->where([
                    'and',
                    [$this->keyParentId => $parent],
                ])
                ->all($this->db);

        foreach ($data as $row) {
            self::createMaterializedPath($row[$this->keyId], sprintf("%s.%s.", $parentPath, $row[$this->keyId]));
        }
        
    }

}
