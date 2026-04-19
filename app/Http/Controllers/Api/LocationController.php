<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Models\Country;
use App\Models\City;
use App\Http\Resources\CountryResource;
use App\Http\Resources\CityResource;

class LocationController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/locations/countries",
     *      tags={"Location"},
     *      summary="Get all countries",
     *      description="Get all countries",
     *      @OA\Response(
     *          response=200,
     *          description="Countries fetched successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="status", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="Countries fetched successfully"),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="Egypt"),
     *                      @OA\Property(property="translations", type="array",
     *                          @OA\Items(type="object",
     *                              @OA\Property(property="language_code", type="string", example="en"),
     *                              @OA\Property(property="name", type="string", example="Egypt"),
     *                          )
     *                      )
     *                  )
     *              ),
     *              @OA\Property(property="errors", type="null", example=null)
     *          )   
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="status", type="integer", example=400),
     *              @OA\Property(property="message", type="string", example="Bad request"),
     *              @OA\Property(property="data", type="null", example=null),
     *              @OA\Property(property="errors", type="null", example=null)
     *          )
     *      )
     * )
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @OA\Get(
     *      path="/api/v1/locations/countries/{country_id}/cities",
     *      tags={"Location"},
     *      summary="Get cities by country id",
     *      description="Get cities by country id",
     *      @OA\Parameter(name="country_id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="Cities fetched successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="status", type="integer", example=200),
     *              @OA\Property(property="message", type="string", example="Cities fetched successfully"),
     *              @OA\Property(property="data", type="array", @OA\Items(type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="name", type="string", example="Cairo" ), @OA\Property(property="translations", type="array", @OA\Items(type="object", @OA\Property(property="language_code", type="string", example="en"), @OA\Property(property="name", type="string", example="Cairo"))))),
     *              @OA\Property(property="errors", type="null", example=null)
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="status", type="integer", example=400),
     *              @OA\Property(property="message", type="string", example="Bad request"),
     *              @OA\Property(property="data", type="null", example=null),
     *              @OA\Property(property="errors", type="null", example=null)
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Country not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="status", type="integer", example=404),
     *              @OA\Property(property="message", type="string", example="Country not found"),
     *              @OA\Property(property="data", type="null", example=null),
     *              @OA\Property(property="errors", type="null", example=null)
     *          )
     *      )
     * )
     * @return \Illuminate\Http\JsonResponse
     */
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
