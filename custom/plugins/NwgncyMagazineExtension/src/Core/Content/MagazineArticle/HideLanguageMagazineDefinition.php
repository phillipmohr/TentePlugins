<?php declare(strict_types=1);

namespace Nwgncy\MagazineExtension\Core\Content\MagazineArticle;

use MoorlMagazine\Core\Content\MagazineArticle\Aggregate\MagazineArticleTranslation\MagazineArticleTranslationDefinition;
use Nwgncy\MagazineExtension\Core\Content\MagazineArticle\HideLanguageMagazineEntity;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;

class HideLanguageMagazineDefinition extends MagazineArticleTranslationDefinition
{
    public function getEntityClass(): string
    {
        return HideLanguageMagazineEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            new TranslatedField('hideLanguage'),
        ]);
    }
}
