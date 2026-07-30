<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="L5 Swagger OpenApi",
 *      description="L5 Swagger OpenApi description"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Parameter(
 *     parameter="Accept-Language",
 *     name="Accept-Language",
 *     in="header",
 *     required=false,
 *     description="Preferred language for application response (ar, en)",
 *
 *     @OA\Schema(
 *         type="string",
 *         default="ar",
 *         enum={"ar", "en"}
 *     )
 * )
 */
abstract class Controller
{
    /**
     * @OA\Get(
     *      path="/api/documentation",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function documentation()
    {
        //
    }
    //
}
