<?php declare(strict_types=1);

namespace Nwgncy\MagazineExtension\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1712753305AddHideLanguageField extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1712753305;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `moorl_magazine_article_translation`
ADD `hide_language` TINYINT AFTER `seo_url`;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
