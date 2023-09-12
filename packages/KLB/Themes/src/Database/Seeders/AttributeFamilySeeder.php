<?php

namespace KLB\Themes\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Attribute\Models\AttributeFamily;
use Webkul\Attribute\Models\AttributeGroup;

class AttributeFamilySeeder extends Seeder
{
    private $attributeGroups = [
        [
            'name'                => 'General',
            'position'            => '1',
            'is_user_defined'     => '0',
        ], [
            'name'                => 'Description',
            'position'            => '2',
            'is_user_defined'     => '0',
        ], [
            'name'                => 'Meta Description',
            'position'            => '3',
            'is_user_defined'     => '0',
        ], [
            'name'                => 'Price',
            'position'            => '4',
            'is_user_defined'     => '0',
        ], [
            'name'                => 'Shipping',
            'position'            => '5',
            'is_user_defined'     => '0',
        ],
    ];

    private $attributeMappings = [
        [
            'attribute_id'        => '1',
            'attribute_group_id'  => '1',
            'position'            => '1',
        ], [
            'attribute_id'        => '2',
            'attribute_group_id'  => '1',
            'position'            => '2',
        ], [
            'attribute_id'        => '3',
            'attribute_group_id'  => '1',
            'position'            => '3',
        ], [
            'attribute_id'        => '4',
            'attribute_group_id'  => '1',
            'position'            => '4',
        ], [
            'attribute_id'        => '5',
            'attribute_group_id'  => '1',
            'position'            => '5',
        ], [
            'attribute_id'        => '6',
            'attribute_group_id'  => '1',
            'position'            => '6',
        ], [
            'attribute_id'        => '7',
            'attribute_group_id'  => '1',
            'position'            => '7',
        ], [
            'attribute_id'        => '8',
            'attribute_group_id'  => '1',
            'position'            => '8',
        ], [
            'attribute_id'        => '9',
            'attribute_group_id'  => '2',
            'position'            => '1',
        ], [
            'attribute_id'        => '10',
            'attribute_group_id'  => '2',
            'position'            => '2',
        ], [
            'attribute_id'        => '11',
            'attribute_group_id'  => '4',
            'position'            => '1',
        ], [
            'attribute_id'        => '12',
            'attribute_group_id'  => '4',
            'position'            => '2',
        ], [
            'attribute_id'        => '13',
            'attribute_group_id'  => '4',
            'position'            => '3',
        ], [
            'attribute_id'        => '14',
            'attribute_group_id'  => '4',
            'position'            => '4',
        ], [
            'attribute_id'        => '15',
            'attribute_group_id'  => '4',
            'position'            => '5',
        ], [
            'attribute_id'        => '16',
            'attribute_group_id'  => '3',
            'position'            => '1',
        ], [
            'attribute_id'        => '17',
            'attribute_group_id'  => '3',
            'position'            => '2',
        ], [
            'attribute_id'        => '18',
            'attribute_group_id'  => '3',
            'position'            => '3',
        ], [
            'attribute_id'        => '22',
            'attribute_group_id'  => '5',
            'position'            => '4',
        ], [
            'attribute_id'        => '23',
            'attribute_group_id'  => '1',
            'position'            => '10',
        ], [
            'attribute_id'        => '24',
            'attribute_group_id'  => '1',
            'position'            => '11',
        ], [
            'attribute_id'        => '25',
            'attribute_group_id'  => '1',
            'position'            => '12',
        ], [
            'attribute_id'        => '26',
            'attribute_group_id'  => '1',
            'position'            => '9',
        ]
    ];

    private $shopifyFamily;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->addShopifyAttributeFamily();
        $this->addAttributeGroups();
        $this->addAttributeGroupMappings();
    }

    private function addShopifyAttributeFamily()
    {
        $family = AttributeFamily::firstOrCreate([
            'code' => 'shopify',
            'name' => 'Shopify',
        ]);

        $family->status = 1;
        $family->is_user_defined = 1;
        $family->save();

        $this->shopifyFamily = $family;
    }

    private function addAttributeGroups()
    {
        $groups = [];
        foreach ($this->attributeGroups as $attributeGroup) {
            $attributeGroup['attribute_family_id'] = $this->shopifyFamily->id;
            array_push($groups, AttributeGroup::firstOrCreate($attributeGroup));
        }
        $this->attributeGroups = $groups;
    }

    private function addAttributeGroupMappings()
    {
        foreach ($this->attributeMappings as $attributeMapping) {
            // The AttributeGroups property will be an array of Models for each
            // of the attribute groups that we just added in the previous step.
            // Use their index in the array as their identifying id to add
            // attributes to. Subtract 1 because the array is 0-indexed and the
            // data is 1-indexed.
            $attributeGroup = $this->attributeGroups[intval($attributeMapping['attribute_group_id']) - 1];
            $attributeId = $attributeMapping['attribute_id'];
            $attributes = $attributeGroup->custom_attributes();
            if (!$attributes->find($attributeId)) {
                // Only attach the attribute if the attribute group does not
                // already have it in its attributes.
                $attributes->attach($attributeId, ['position' => $attributeMapping['position']]);
            }
        }
    }

}
