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
}
