<?php

namespace KLB\Themes\Http\Controllers\Admin;

use Illuminate\Database\Query\Builder;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Symfony\Component\ErrorHandler\Error\ClassNotFoundError;

class TableSearchController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');

        $this->_config = request('_config');
    }

    /**
     * This method takes a given model class and looks for all instances of it.
     * These instances are then mapped down to a single attribute and returned
     * in a collection for use as all available options in an autocomplete
     * field.
     *
     * It requires a fully qualified model and a searchBy field to be sent as
     * parameter data within the request in order to function.
     *
     * For now, the only class utilizing this method is the Table.Blade.PHP file
     * which renders datagrids for Bagisto. You can find an object called
     * 'autoCompletePages' within the Vue component's data attributes that
     * houses the available pages and their associated Model and SortBy property
     * used here.
     *
     * If you want another datagrid to have autocomplete text search, simply
     * copy one of the objects in the autoCompletePages object and fill in the
     * information for the new datagrid. No further changes should be required.
     *
     * If you supply a queryScope parameter, it will use that scope on the model
     * and return its result, instead of plucking the 'searchBy field'. The
     * file this was created for is the edit.blade.php file under the Webkul
     * Admin Catalog Category directory.
     *
     * @throws ClassNotFoundError
     *
     * @return Collection | null
     */
    public function autoCompleteOptions()
    {
        $model = request()->input('model');
        $searchBy = request()->input('searchBy');
        $queryScope = request()->input('queryScope');

        if ($queryScope) {
            $results = $model::$queryScope();

            return $results instanceof Builder ? $results->get() : $results;
        }

        return $model::pluck($searchBy);
    }
}
