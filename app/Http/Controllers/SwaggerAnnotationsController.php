<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 *    @OA\Info(
 *      version="3.0.0",
 *      title="Anna Borisenko Documentation"
 *    ),
 *     @OA\PathItem(
 *         path="/api/"
 *     )
 * @OA\Post(
 *     path="/login",
 *     tags={"Login"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 type="object",
 *                 @OA\Property(
 *                     property="email",
 *                     type="string",
 *                     example="exampleuser@gmail.com"
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="string",
 *                     example="123456"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response="200", description="Login successful"),
 *    @OA\Response(
 *      response=401,
 *      description="Unauthorized",
 *      @OA\JsonContent(
 *          oneOf={
 *              @OA\Schema(
 *                  @OA\Property(property="message", type="string", example="Invalid credentials"),
 *              ),
 *              @OA\Schema(
 *                  @OA\Property(property="message", type="string", example="Blocked User"),
 *              ),
 *              @OA\Schema(
 *                  @OA\Property(property="message", type="string", example="You do not have access rights"),
 *              )
 *          }
 *      )
 *  )
 * )
 */
class SwaggerAnnotationsController extends Controller
{

}
