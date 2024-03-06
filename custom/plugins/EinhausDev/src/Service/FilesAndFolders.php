<?php declare(strict_types=1);

namespace SnippetsImport\Service;

use League\Flysystem\FilesystemInterface;
use Shopware\Core\Framework\Adapter\Filesystem\PrefixFilesystem;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Filesystem\Filesystem;

class FilesAndFolders
{ 

    public function __construct(
        FilesystemOperator $shopwareFileSystemPrivate,
        FilesystemOperator $shopwareFileSystemPublic,
        FilesystemOperator $shopwareFileSystemAsset,
        FilesystemOperator $shopwareFileSystemSitemap,
        FilesystemOperator $shopwareFileSystemTheme
        )
    {    
        $this->shopwareFileSystemPrivate = $shopwareFileSystemPrivate;
        $this->shopwareFileSystemPublic = $shopwareFileSystemPublic;
        $this->shopwareFileSystemAsset = $shopwareFileSystemAsset;
        $this->shopwareFileSystemSitemap = $shopwareFileSystemSitemap;
        $this->shopwareFileSystemTheme = $shopwareFileSystemTheme;
    }

    public function getPluginFileSystemPublic() {
        return $this->pluginFileSystemPublic;
    }

    public function getPluginFileSystemPrivate() {
        return $this->pluginFileSystemPrivate;
    }

    public function getShopwareFileSystemPrivate() {
        return $this->shopwareFileSystemPrivate;
    }

    public function getShopwareFileSystemPublic() {
        return $this->shopwareFileSystemPublic;
    }

    public function getShopwareFileSystemAsset() {
        return $this->shopwareFileSystemAsset;
    }

    public function getShopwareFileSystemSitemap() {
        return $this->shopwareFileSystemSitemap;
    }

    public function getShopwareFileSystemTheme() {
        return $this->shopwareFileSystemTheme;
    }

    public function getFilesFromPublicFolder($path) {

        $swFileSystemPublic = $this->getShopwareFileSystemPublic();
        // \Kint::dump(get_class($swFileSystemPublic));
        return $swFileSystemPublic->listContents($path, true);
    }

    // the path stops just one before the path the filesystem coveres EX:
   //$test1 = $filesAndFolders->getShopwareFileSystemTheme()->listContents('theme', true);
    public function listPrivatePluginFiles() {

        return $this->pluginFileSystemPrivate->listContents($filename);
    }
    public function listPublicPluginFiles() {

        return $this->pluginFileSystemPublic->listContents($filename);
    }

    public function generateRandomString($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    public function listPublicFiles($path) {
        // "plugins/einhaus_digital_development/"
        return $this->pluginFileSystemPublic->listContents($path);
    }

} 
