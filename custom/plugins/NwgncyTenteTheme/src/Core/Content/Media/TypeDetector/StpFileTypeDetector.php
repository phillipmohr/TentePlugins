<?php declare(strict_types=1);

namespace Nwgncy\TenteTheme\Core\Content\Media\TypeDetector;

use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Content\Media\MediaType\DocumentType;
use Shopware\Core\Content\Media\MediaType\MediaType;
use Shopware\Core\Content\Media\TypeDetector\TypeDetectorInterface;

class StpFileTypeDetector implements TypeDetectorInterface
{
    protected const SUPPORTED_FILE_EXTENSIONS = [
        'stp' => ['application/STEP'],
    ];

    public function detect(MediaFile $mediaFile, ?MediaType $previouslyDetectedType): ?MediaType
    {
        $fileExtension = mb_strtolower($mediaFile->getFileExtension());
        if (!\array_key_exists($fileExtension, self::SUPPORTED_FILE_EXTENSIONS)) {
            return $previouslyDetectedType;
        }

        if ($previouslyDetectedType === null) {
            $previouslyDetectedType = new DocumentType();
        }
        

        $previouslyDetectedType->addFlags(self::SUPPORTED_FILE_EXTENSIONS[$fileExtension]);

        return $previouslyDetectedType;
    }
}