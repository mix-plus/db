# 介绍

新建model继承`AbstractModel`,配置数据表名,主键

```php
<?php

namespace App\Model;

use MixPlus\Framework\Database\AbstractModel;

class UserModel extends AbstractModel
{
    protected string $primaryKey = 'user_id';

    protected string $table = 'kd_farm_account';
}
```

使用

```php
$user = (new UserModel())->getOneId(414, [
    'user_id', 'nickname'
]);
```