<?php
namespace ank\migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\Version;

/**
 * 迁移工具版本基类
 */
class BaseVersion extends AbstractMigration
{
    protected $app = null;

    public function __construct(Version $version)
    {
        parent::__construct($version);
        $this->app = \ank\App::getInstance();
    }

    public function down(Schema $schema): void
    {

    }

    public function exec($query, array $params = [], array $types = [])
    {
        return $this->connection->executeUpdate($query, $params, $types);
    }

    public function up(Schema $schema): void
    {

    }

    protected function addSql(string $sql, array $params = [], array $types = []): void
    {

        $sql = $this->replaceSql($sql);
        parent::addSql($sql, $params, $types);
    }

    /**
     * 替换sql中的表前缀
     * @param string $sql
     * @param array  $arr
     * @return string
     */
    protected function replaceSql(string $sql, $arr = [])
    {
        $config = $this->app->config('db_config');

        return strtr($sql, array_merge(
            [
                '__PREFIX__' => $config['prefix'],
            ],
            $arr)
        );
    }

    /**
     * 数据库是否存在,表格式为不带前缀的下划线格式
     */
    protected function tableExist(Schema $schema, string $tableName)
    {
        return $schema->hasTable($this->replaceSql('__PREFIX__' . $tableName));
    }

    /**
     * 指定表的字段是否存在
     */
    protected function fieldExist(Schema $schema, string $fieldName, string $tableName)
    {
        if (!$this->tableExist($schema, $table)) {
            return false;
        }
        $table = $schema->getTable($this->replaceSql('__PREFIX__' . $tableName));
        return $table->hasColumn($fieldName);
    }

    /**
     * 查询数据
     * @param       $sql
     * @param array $params
     * @param array $types
     */
    protected function fetchAll($sql, array $params = [], $types = [])
    {
        return $this->connection->fetchAll($sql, $params, $types);
    }

}
