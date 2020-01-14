# ank-migrations
# 项目配置
在项目的公共配置文件中添加迁移配置，不要添加到业务访问模块
``` php
'migrations'  => [
    'name'       => 'Stat Db Migrations',
    'name_space' => 'migration',
    'table_name' => 'kl_migration',
    'paths'      => [dirname(dirname(__DIR__)) . '/migration'],
],
```
 * name 为项目的名字,可自定义
 * name_space 为迁移类的命名空间，尽量保持默认，跟框架的命名空间规则一至
 * table_name 迁移类在数据库生成的迁移记录表名字
 * paths 数组,迁移类所在路径(绝对路径),一般路径跟 controller 目录同级(跟框架目录规则一至)
 以上配置可以在多个项目中设置，迁移命令执行的时候会自动合并，但顺序不确定，所以建议除啦 **paths** 外其它配置项只配置一次

# 生成迁移脚本
``` bash
./vendor/bin/ank-db migrations:generate
```

# 执行迁移到最新版本
``` bash
./vendor/bin/ank-db migrations:migrate
```

# --dry-run是空转参数，只显示操作结果，不执行修改
``` bash
./vendor/bin/ank-db migrations:migrate --dry-run
```

# 不执行操作，只写入文件，对于生产环境需要手动验证并执行的场景有用
``` bash
./vendor/bin/ank-db migrations:migrate --write-sql=file.sql
```

# 查看详细信息
``` bash
./vendor/bin/ank-db status
```

# 迁移到指定版本
``` bash
./vendor/bin/ank-db migrations:migrate 20180608161758
```

# 使用别名迁移到指定版本
 * first:回退到初始版本
 * prev:回到上一个版本
 * next:迁移到下一个版本
 * latest:迁移到最新版本（和不加版本号效果一样）