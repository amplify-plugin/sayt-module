<?php

namespace Amplify\System\Sayt\Http\Controllers;

use Amplify\System\Abstracts\BackpackCustomCrudController;
use Amplify\System\Backend\Models\Category;
use Amplify\System\Backend\Models\SystemConfiguration;
use Amplify\System\Backend\Traits\SettingOperation;
use Amplify\System\Sayt\Seeders\SaytSettingSeeder;
use Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SaytSettingController
 *
 * @property-read CrudPanel $crud
 */
class SaytSettingController extends BackpackCustomCrudController
{
    use SettingOperation;
    use FetchOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(SystemConfiguration::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/sayt-setting');
        CRUD::setEntityNameStrings('sayt-setting', 'SAYT Settings');
    }

    public function getSettingName(): string
    {
        return 'sayt';
    }

    public function getSeederClass(): ?string
    {
        return SaytSettingSeeder::class;
    }

    protected function fetchCatalogs()
    {
        return $this->fetch([
            'model' => Category::class,
            'paginate' => false,
            'query' => fn($query) => $query->whereNull('parent_id')
        ]);
    }
}