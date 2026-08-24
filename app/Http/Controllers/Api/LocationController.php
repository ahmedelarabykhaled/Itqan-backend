<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\CountryResource;
use App\Http\Responses\ApiResponse;
use App\Models\City;
use App\Models\Country;

class LocationController extends Controller
{
    public function getCountries()
    {
        $countries = Country::with('customTranslations')->get();

        // return $countries;
        return ApiResponse::success(
            message: __('messages.countries_fetched_successfully'),
            data: CountryResource::collection($countries),
            status: 200
        );
    }

    public function getCitiesByCountryId($country_id)
    {
        $cities = City::with('customTranslations')->where('country_id', $country_id)->get();

        return ApiResponse::success(
            message: __('messages.cities_fetched_successfully'),
            data: CityResource::collection($cities),
            status: 200
        );
    }
}
