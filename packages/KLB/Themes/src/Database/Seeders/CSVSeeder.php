<?php

namespace KLB\Themes\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use KLB\Themes\Traits\CSVReader;
use Webkul\Core\Eloquent\Repository;

abstract class CSVSeeder extends Seeder implements SeederInterface
{
    use CSVReader;

    protected $csv;
    protected $csvPath;
    protected $failed = 0;
    protected $successes = 0;
    protected $unescapeProperties = [];
    protected $escapedCommaString = '&#44;';
    protected $sortCsv = true;

    abstract public function create($model);

    public function sort($model, $key)
    {
        return $key;
    }

    public function seedFromCsv()
    {
        $this->csv->each(function ($model) {
            $model = $this->explodeEscapedCommas($model);
            if ($this->create($model)) {
                $this->successes += 1;
            } else {
                $this->failed += 1;
                Log::debug(json_encode($model).' not created');
            }
        });

        Log::debug("Imported $this->successes items successfully. $this->failed items were not imported.");
    }

    public function sortCsv()
    {
        if ($this->sortCsv) {
            $this->csv = $this->csv->sortBy(function ($model, $key) {
                return $this->sort($model, $key);
            });
        }
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->csv = $this->readCsv($this->csvPath);
        $this->sortCsv();
        $this->seedFromCsv();
    }

    /**
     * Explode the values of all keys present in the given $model if they are
     * present in the $this->unescapeProperties array. The delimiter used in
     * explode() is the $this->escapedCommaString
     *
     * @param array $model - The array of data (associative) to
     *
     * @return array - The model array with mutated attributes (values)
     */
    protected function explodeEscapedCommas(array $model)
    {
        if ($this->unescapeProperties) {
            foreach ($this->unescapeProperties as $propertyKey => $propertyValue) {
                // For associative arrays, the $propertyValue will be a boolean.
                // This will cover the case if we want to join the array back to
                // string or not. In this case, $propertyKey will be the
                // $property we are looking to unescape. Otherwise (for
                // non-associative arrays) the key we're looking for will be the
                // value of the key => value pair in the array.
                $property = !is_bool($propertyValue) ? $propertyValue : $propertyKey;
                if (array_key_exists($property, $model)) {
                    $model[$property] = explode($this->escapedCommaString, $model[$property]);
                    if ($propertyValue === true && $property == $propertyKey) {
                        $model[$property] = join(', ', $model[$property]);
                    }
                }
            }
        }

        return $model;
    }
}
