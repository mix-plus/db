<?php

namespace MixPlus\Db\MySQL;

interface ModelInterface
{
    public function getOneId(int $id, array $columns = ['*']): array;

    public function findByWhere(array $where = [], array $columns = ['*'], array $options = []): array;

    public function getManyByIds(array $ids, array $columns = ['*']): array;

    public function getManyByWhere(array $where = [], array $columns = ['*'], array $options = []): array;

    public function getPageList(array $where = [], array $columns = ['*'], array $options = []): array;

    public function updateById(int $id, array $data): int;

    public function updateByIds(array $ids, array $data): int;

    public function updateByWhere(array $where, array $data): int;

    public function deleteOne(int $id): int;

    public function deleteAll(array $ids): int;

    public function deleteByWhere(array $where): int;

    public function createOne(array $data): int;

    public function createAll(array $data): int;
}
