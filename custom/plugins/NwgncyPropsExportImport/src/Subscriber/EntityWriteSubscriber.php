<?php declare(strict_types=1);

namespace NwgncyPropsExportImport\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use NwgncyPropsExportImport\Service\Property;
use NwgncyPropsExportImport\Service\Language;
use Shopware\Core\Framework\Context;

class EntityWriteSubscriber implements EventSubscriberInterface
{
    protected $property;

    protected $language;
    // Command name
    public function __construct(
        Property $property,
        Language $language
    ) {
        $this->property = $property;
        $this->language = $language;
    }

    public static function getSubscribedEvents()
    {
        return [
            'api.property_group.update.request' => 'beforeWrite',
        ];
    }

    public function beforeWrite($event)
    {

        $context = Context::createDefaultContext();
        $request = $event->getRequest()->request;
        $data = $request->all();

        if (isset($data['customFields'])) {

            $groupId = $data['id'];
            $languagesIdName = $this->language->getLanguagesIdName($context); 
            $customFields = $data['customFields'];

            $translationsArr = [];
            foreach ($languagesIdName as $languageId => $languageName) {
                $translationsArr[$languageId]['customFields'] = $customFields;
            }
    
            $this->property->updatePropertyGroupTranslationByGroupId($context, $groupId, $translationsArr);

        }
    }
}