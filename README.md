# ank-migrations
# 生成迁移脚本
./vendor/bin/ank-db migrations:generate
# 执行迁移到最新版本
./vendor/bin/ank-db migrations:migrate
# --dry-run是空转参数，只显示操作结果，不执行修改
./vendor/bin/ank-db migrations:migrate --dry-run
# 不执行操作，只写入文件，对于生产环境需要手动验证并执行的场景有用
./vendor/bin/ank-db migrations:migrate --write-sql=file.sql
# 查看详细信息
./vendor/bin/ank-db status
# 迁移到指定版本
./vendor/bin/ank-db migrations:migrate 20180608161758