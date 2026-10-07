<?php

namespace Amplify\System\Sayt\Seeders;

use Amplify\System\Backend\Models\SystemConfiguration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SaytSettingSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->data() as $datum) {
            $datum['name'] = 'sayt';
            SystemConfiguration::seed($datum);
        }
    }

    private function data()
    {
        return [
            [
                'option' => 'product_search_by_id_prefix',
                'value' => 'Products.Product Id',
                'type' => 'string',
                'field' => [
                    'name' => 'value',
                    'type' => 'text',
                    'label' => 'EasyAsk Product Search Key',
                ],
            ],
            [
                'option' => 'search_box_placeholder',
                'value' => 'Please tell us what you are looking for...',
                'type' => 'string',
                'field' => [
                    'name' => 'value',
                    'type' => 'text',
                    'label' => 'EasyAsk Search Box Placeholder',
                ],
            ],
            [
                'option' => 'use_product_restriction',
                'value' => false,
                'type' => 'bool',
                'field' => [
                    'name' => 'value',
                    'type' => 'boolean',
                    'label' => 'Use Product Restriction Control',
                    'hint' => 'If enabled, the system will attach search restriction parameters into the easyask search.',
                ],
            ],
            [
                'option' => 'use_multiple_catalog',
                'value' => false,
                'type' => 'bool',
                'field' => [
                    'name' => 'value',
                    'type' => 'boolean',
                    'label' => 'Use Multiple Catalog',
                    'hint' => 'If enabled, the system will use different catalog per customer based on customer-group assignment.',
                ],
            ],
            [
                'option' => 'default_catalog',
                'value' => null,
                'type' => 'integer',
                'field' => [
                    'name' => 'value',
                    'type' => 'select2_from_ajax',
                    'attribute' => 'category_name',
                    'data_source' => backpack_url('sayt-setting/fetch/catalogs'),
                    'label' => 'Default Catalog',
                    'hint' => 'This catalog will be used for all customers that are not assigned to a specific catalog.',
                    'delay' => 300,
                    'placeholder' => 'Select a catalog',
                    'minimum_input_length' => 0,
                    'model' => \Amplify\System\Backend\Models\Category::class,
                    'method' => 'POST',
                ],
            ],
            [
                'option' => 'suggestion_limit',
                'value' => 5,
                'type' => 'integer',
                'field' => [
                    'label' => 'EasyAsk Result Suggestion Limit',
                    'name' => 'value',
                    'type' => 'number',
                    'attributes' => [
                        'min' => 1,
                        'max' => 10,
                        'step' => 1,
                    ],
                    'hint' => 'Maximum number of suggestions terms display in search dropdown.',
                ]
            ]
        ];
    }
}
