<?php

namespace KLB\Themes\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Attribute\Models\Attribute;

class SizeAndColorAttributeModifications extends Seeder
{
    private function makeTextSwatch(Model $model)
    {
        $model->swatch_type = 'text';
    }

    private function makeVisibleOnFrontEnd(Model $model)
    {
        $model->is_visible_on_front = 1;
    }

    private function modifyModel(Model $model)
    {
        $this->makeTextSwatch($model);
        $this->makeVisibleOnFrontEnd($model);
        $model->save();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Attribute::where('code', 'size')->orWhere('code', 'color')->get()->each(function (Model $model) {
            $this->modifyModel($model);
        });
    }
}
