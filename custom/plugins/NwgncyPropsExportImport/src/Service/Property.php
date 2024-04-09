<?php declare(strict_types=1);

namespace NwgncyPropsExportImport\Service;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;

class Property
{
    protected $propertyGroup;

    protected $propertyGroupOption; 
    protected $propertyGroupTranslation; 

    public function __construct(
        EntityRepository $propertyGroup,
        EntityRepository $propertyGroupOption,
        EntityRepository $propertyGroupTranslation
    )
    {
        $this->propertyGroup = $propertyGroup;
        $this->propertyGroupOption = $propertyGroupOption;
        $this->propertyGroupTranslation = $propertyGroupTranslation;
    }

    public function getAllPropertyGroupOptions($context)
    {
 
         $criteria = (new Criteria())
            ->addAssociation('name')
            ->addAssociation('group')
            ->addAssociation('translations')
            ->addAssociation('language');
            
        return $this->propertyGroupOption->search($criteria, $context);
    }

    public function getAllPropertyGroups($context)
    {
 
         $criteria = (new Criteria())
            ->addAssociation('name')
            ->addAssociation('translations');
            
        return $this->propertyGroup->search($criteria, $context);
    }
    public function getPropertyGroupTranslations($context, $groupId)
    {
          $criteria = (new Criteria())
            ->addAssociation('translations')
            ->addFilter(new EqualsFilter('id', $groupId));

        return $this->propertyGroup->search($criteria, $context);
    }
    
    public function getPropertyGroupById($context, $groupId)
    {
          $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('id', $groupId));

        return $this->propertyGroup->search($criteria, $context);
    }
    
    public function updatePropertyGroupTranslationByGroupId($context, $groupId, $translations)
    {

        $data = [
            [
                'id' => $groupId,
                'translations' => $translations
            ]
        ];
        return $this->propertyGroup->update($data, $context);
    }

    public function updatePropertyGroupTranslation($context, $groupId, $languageId, $translation)
    {
        $result = $this->propertyGroupTranslation->update([
            [
                'property_group_id' => $groupId,
                'language_id' => $languageId,
                'name' => $translation
            ]
        ], $context);

        return $result;
    }
 
    public function createPropertyGroupTranslation($context, $groupId, $languageId, $translation)
    {

        $data = [
            'property_group_id' => $groupId,
            'language_id' => $languageId,
            'name' => $translation
        ];

        $result =  $this->propertyGroupTranslation->create([$data], $context);
    
        return $result;
    }
}