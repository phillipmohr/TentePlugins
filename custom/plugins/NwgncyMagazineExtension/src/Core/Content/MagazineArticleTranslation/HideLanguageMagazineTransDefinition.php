<?php declare(strict_types=1);

namespace Nwgncy\MagazineExtension\Core\Content\MagazineArticleTranslation;

use MoorlMagazine\Core\Content\MagazineArticle\Aggregate\MagazineArticleTranslation\MagazineArticleTranslationDefinition;
use Nwgncy\MagazineExtension\Core\Content\MagazineArticleTranslation\HideLanguageMagazineTransEntity;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;

class HideLanguageMagazineTransDefinition extends MagazineArticleTranslationDefinition
{
    public function getEntityClass(): string
    {
        return HideLanguageMagazineTransEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            new BoolField('hide_language', 'hideLanguage'),
        ]);
    }
}
