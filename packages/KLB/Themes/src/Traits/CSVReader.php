<?php

namespace KLB\Themes\Traits;

use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

trait CSVReader
{
    public function readCsv($path)
    {
        $datafile = Storage::disk('klb')->get($path);
        $reader = Reader::createFromString($datafile);
        $reader->setHeaderOffset(0);

        return collect($reader);
    }
}