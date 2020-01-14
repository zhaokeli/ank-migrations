<?php
namespace ank\migration;

use Doctrine\Migrations\Finder\Finder;
use Doctrine\Migrations\Finder\MigrationDeepFinder;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

/**
 * 迁移工具查找
 */
class MigrationFinder extends Finder implements MigrationDeepFinder
{
    /**
     * @return string[]
     */
    public function findMigrations(string $directory,  ? string $namespace = null) : array
    {
        $dirs  = explode(',', $directory);
        $files = [];
        foreach ($dirs as $key => $dir) {
            $dir   = $this->getRealPath($dir);
            $files = array_merge($files, $this->getMatches($this->createIterator($dir)));
        }

        return $this->loadMigrations(
            $files,
            $namespace
        );
    }

    private function createIterator(string $dir): RegexIterator
    {
        return new RegexIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS),
                RecursiveIteratorIterator::LEAVES_ONLY
            ),
            $this->getPattern(),
            RegexIterator::GET_MATCH
        );
    }

    /**
     * @return string[]
     */
    private function getMatches(RegexIterator $iteratorFilesMatch): array
    {
        $files = [];
        foreach ($iteratorFilesMatch as $file) {
            $files[] = $file[0];
        }

        return $files;
    }

    private function getPattern(): string
    {
        return sprintf(
            '#^.+\\%sVersion[^\\%s]{1,255}\\.php$#i',
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR
        );
    }
}
