<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="L5 Swagger OpenApi",
 *      description="L5 Swagger OpenApi description"
 * )
 */
abstract class Controller
{
    /**
     * @OA\Get(
     *      path="/api/documentation",
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
