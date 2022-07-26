<?php

namespace MixPlus\Db\MySQL;

use Mix\Database\ConnectionInterface;
use MixPlus\Db\Cache\Cache;

abstract class AbstractModel implements ModelInterface
{
    protected string $table;

    protected string $primaryKey = 'id';

    public function getDb()
    {
        return new BaseModel(config('database'));
    }

    public function getOneId(int $id, array $columns = ['*']): array
    {
        $columns = implode(',', $columns);
        $key = $this->table . "_cache:{$this->primaryKey}_{$id}:" . md5("id_{$id},columns_{$columns}");
        $data = (new Cache)->getOrSet($key, function () use ($id, $columns) {
            $data = $this->getDb()->get($this->table, $columns, [
                "{$this->primaryKey}[=]" => $id,
            ]);
            return serialize($data);
        });
        return (array)unserialize($data);
    }

    public function findByWhere(array $where = [], array $columns = ['*'], array $options = []): array
    {
        $columns = implode(',', $columns);
        $tmpOptions = implode(',', $options);
        $tmpWhere = implode(',', $where);
        $key = $this->table . "_cache:" . md5("where:{$tmpWhere},columns:{$columns},options:{$tmpOptions}");
        foreach ($where as $value) {
            if ($value[0] == $this->primaryKey) {
                $key = $this->table . "_cache:{$this->primaryKey}_{$value[2]}:" . md5("where_{$tmpWhere},columns_{$columns},options_{$tmpOptions}");
            }
        }

        $data = (new Cache)->getOrSet($key, function () use ($where, $columns, $options) {
            $db = db()->table($this->table);
            $data = $this->optionWhere($db, $where, $options)->select($columns)->first();
            return serialize($data);
        });

        return (array)unserialize($data);
    }

    public function getManyByIds(array $ids, array $columns = ['*']): array
    {
        return db()->table($this->table)->where("{$this->primaryKey} IN {?}", $ids)->select(implode($columns))->first();
    }

    public function getManyByWhere(array $where = [], array $columns = ['*'], array $options = []): array
    {
        $db = db()->table($this->table);
        return $this->optionWhere($db, $where, $options)->select(implode($columns))->get();
    }

    public function getPageList(array $where = [], array $columns = ['*'], array $options = []): array
    {
        $db = db()->table($this->table);
        $offset = $options['page'] ?? 1;

        $limit = $options['size'] ?? 15;
        return $this->optionWhere($db, $where, $options)
            ->select(implode($columns))
            ->offset(($offset - 1) * $limit)
            ->limit($limit)
            ->get();
    }

    public function updateById(int $id, array $data): int
    {
        // $this->table . "_cache:{$this->primaryKey}_{$value[2]}:" . md5("where_{$tmpWhere},columns_{$columns},options_{$tmpOptions}");
//        $key = $this->table . "_cache:{$this->primaryKey}_{$id}:";
//        (new Cache)->del()
        return db()->table($this->table)->where("{$this->primaryKey} = ?", $id)->updates($data)->rowCount();
    }

    public function updateByIds(array $ids, array $data): int
    {
        // TODO 删除缓存
        return db()->table($this->table)->where("{$this->primaryKey} IN {?}", $ids)->updates($data)->rowCount();
    }

    public function updateByWhere(array $where, array $data): int
    {
        // TODO 删除缓存
        $db = db()->table($this->table);
        return $this->optionWhere($db, $where, [])->updates($data)->rowCount();
    }

    public function deleteOne(int $id): int
    {
        // TODO 删除缓存
        return db()->table($this->table)->where("{$this->primaryKey} = ?", $id)->delete()->rowCount();
    }

    public function deleteAll(array $ids): int
    {
        // TODO 删除缓存
        return db()->table($this->table)->where("{$this->primaryKey} IN {?}", $ids)->delete()->rowCount();
    }

    public function deleteByWhere(array $where): int
    {
        // TODO 删除缓存
        $db = db()->table($this->table);
        return $this->optionWhere($db, $where, [])->delete()->rowCount();
    }

    public function createOne(array $data): int
    {
        return db()->insert($this->table, $data)->rowCount();
    }

    public function createAll(array $data): int
    {
        return db()->batchInsert($this->table, $data)->rowCount();
    }

    protected function optionWhere(ConnectionInterface $db, array $where, array $options)
    {
        if (!empty($where) && is_array($where)) {
            foreach ($where as $k => $v) {
                if (!is_array($v)) {
                    $db->where($k, $v);
                    continue;
                }
                ## 二维索引数组
                if (is_numeric($k)) {
                    $v[1] = mb_strtoupper($v[1]);
                    if (in_array($v[1], ['=', '!=', '<', '<=', '>', '>=', 'LIKE', 'NOT LIKE'])) {
                        $db = $db->where("{$v[0]} {$v[1]} ?", $v[2]);
                    } elseif ($v[1] === 'IN') {
                        $db = $db->where("{$v[0]} IN {?}", $v[2]);
                    } elseif ($v[1] === 'NOT IN') {
                        $db = $db->where("$v[0] NOT IN {?}", $v[2]);
                    } elseif ($v[1] === 'RAW') {
                        $db = $db->where($v[0]);
                    }
                } else {
                    ## 二维关联数组
                    $db = $db->where("{$k} IN {?}", $v);
                }
            }
        }
        isset($options['orderByRaw']) && $db = $db->order($options['orderByRaw'][0], $options['orderByRaw'][1]);
//        isset($options['selectRaw']) && $db = $db->raw($options['selectRaw']);
        return $db;
    }
}
