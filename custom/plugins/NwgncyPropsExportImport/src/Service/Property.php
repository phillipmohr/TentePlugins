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

    public function __construct(
        EntityRepository $propertyGroup,
        EntityRepository $propertyGroupOption
    )
    {
        $this->propertyGroup = $propertyGroup;
        $this->propertyGroupOption = $propertyGroupOption;
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
}