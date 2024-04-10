<?php declare(strict_types=1);

namespace Nwgncy\MagazineExtension\Core\Content\MagazineArticleTranslation;

use MoorlMagazine\Core\Content\MagazineArticle\Aggregate\MagazineArticleTranslation\MagazineArticleTranslationEntity;

class HideLanguageMagazineTransEntity extends MagazineArticleTranslationEntity
{

    protected bool $hideLanguage = false;

    public function getLHideLanguage(): bool
    {
        return $this->hideLanguage;
    }

    public function setLHideLanguage(bool $hideLanguage): void
    {
        $this->hideLanguage = $hideLanguage;
    }

}
