<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DefaultResource;
use App\Models\Country;
use App\Traits\QueryBuilderTrait;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    use QueryBuilderTrait;

    public function index(Request $request)
    {
        $query = $this->generateQuery($request, Country::class);

        $countries = $query->paginate($request->per_page);
        return DefaultResource::collection($countries);
    }
}
