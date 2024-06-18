<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="3.0.0",
 *     title="АПІ Документація для ІС 'Центр дошкільного розвитку'"
 * ),
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Use a valid Bearer token to access endpoints",
 *     name="Authorization",
 *     in="header",
 * )
 * @OA\Schema(
 *      schema="Role",
 *      type="object",
 *      @OA\Property(property="id", type="integer", readOnly=true, example=1),
 *      @OA\Property(property="name", type="string", readOnly=true, example="super_admin")
 *  )
 * @OA\Schema(
 *      schema="Profile",
 *      required={"id", "name"},
 *      @OA\Property(
 *          property="user_id",
 *          type="integer",
 *          example=1
 *      ),
 *      @OA\Property(
 *          property="last_name",
 *          type="string",
 *          example="Doe"
 *      ),
 *      @OA\Property(
 *          property="first_name",
 *          type="string",
 *          example="John"
 *      ),
 *      @OA\Property(
 *          property="patronymic_name",
 *          type="string",
 *          example="Paterson"
 *      ),
 *      @OA\Property(
 *          property="email",
 *          type="string",
 *          example="email@example.com"
 *      ),
 *      @OA\Property(
 *          property="city",
 *          type="string",
 *          example="Example city"
 *      ),
 *      @OA\Property(
 *          property="street",
 *          type="string",
 *          example="Example street"
 *      ),
 *      @OA\Property(
 *          property="house_number",
 *          type="string",
 *          example="12"
 *      ),
 *      @OA\Property(
 *          property="apartment_number",
 *          type="string",
 *          example="12"
 *      ),
 *      @OA\Property(
 *          property="birthdate",
 *          type="string",
 *          example="2000-01-01"
 *      ),
 *      @OA\Property(
 *          property="user_category",
 *          type="string",
 *          example="employee"
 *      ),
 *  )
 * @OA\Post(
 *     path="/login",
 *     tags={"Auth"},
 *     @OA\Parameter(
 *          name="Accept",
 *          in="header",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              default="application/json"
 *          )
 *      ),
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
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="error", type="string", example="Неввірний логін або пароль"),
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="error", type="string", example="Цього користувача було деактивовано"),
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="error", type="string", example="У вас немає прав доступу до системи"),
 *                 )
 *             }
 *         )
 *     )
 * )
 * @OA\Get(
 *     path="/logout",
 *     tags={"Auth"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *          name="Accept",
 *          in="header",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              default="application/json"
 *          )
 *      ),
 *     @OA\Response(
 *         response=200,
 *         description="Logged out successfully",
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *     )
 * )
 * @OA\Get(
 *     path="/logged_user",
 *     tags={"Auth"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *          name="Accept",
 *          in="header",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              default="application/json"
 *          )
 *      ),
 *     @OA\Response(
 *         response=200,
 *         description="Logged in user profile",
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *     )
 * )
 * @OA\Get(
 *      path="/user/profile",
 *      tags={"Profile"},
 *      security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *          name="Accept",
 *          in="header",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              default="application/json"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="User Profile",
 *          @OA\JsonContent(ref="#/components/schemas/Profile")
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Error: Bad Request",
 *          @OA\JsonContent(
 *               type="object",
 *               @OA\Property(property="error", type="string", example="Error message")
 *          )
 *      ),
 *  )
 * @OA\Get(
 *      path="/users/active",
 *      tags={"Users"},
 *      security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *          name="Accept",
 *          in="header",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              default="application/json"
 *          )
 *      ),
 *     @OA\Parameter(
 *           name="page",
 *           in="query",
 *           description="The page number to retrieve.",
 *           required=false,
 *           @OA\Schema(
 *               type="integer",
 *               format="int32"
 *           )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Active userslist "
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Error: Bad Request",
 *          @OA\JsonContent(
 *               type="object",
 *               @OA\Property(property="error", type="string", example="Error message")
 *          )
 *      ),
 *  )
 * @OA\Get(
 *      path="/users/not_active",
 *      tags={"Users"},
 *      security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *          name="Accept",
 *          in="header",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              default="application/json"
 *          )
 *      ),
 *     @OA\Parameter(
 *           name="page",
 *           in="query",
 *           description="The page number to retrieve.",
 *           required=false,
 *           @OA\Schema(
 *               type="integer",
 *               format="int32"
 *           )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Active userslist "
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Error: Bad Request",
 *          @OA\JsonContent(
 *               type="object",
 *               @OA\Property(property="error", type="string", example="Error message")
 *          )
 *      ),
 *  )
 * @OA\Post(
 *      path="/users/create",
 *      tags={"Users"},
 *      security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *          name="Accept",
 *          in="header",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              default="application/json"
 *          )
 *      ),
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(
 *                  type="object",
 *                  required={"last_name", "first_name"},
 *                  @OA\Property(
 *                      property="last_name",
 *                      type="string",
 *                      example="Lastname"
 *                  ),
 *                  @OA\Property(
 *                      property="first_name",
 *                      type="string",
 *                      example="Firstname"
 *                  ),
 *                  @OA\Property(
 *                      property="patronymic_name",
 *                      type="string",
 *                      example="Patronymicname"
 *                  ),
 *                  @OA\Property(
 *                      property="role",
 *                      ref="#/components/schemas/Role"
 *                  ),
 *                   @OA\Property(
 *                       property="email",
 *                       type="string",
 *                       example="email@example.com"
 *                   ),
 *                   @OA\Property(
 *                       property="city",
 *                       type="string",
 *                       example="ExampleCity"
 *                   ),
 *                   @OA\Property(
 *                       property="street",
 *                       type="string",
 *                       example="ExampleStreet"
 *                   ),
 *                   @OA\Property(
 *                       property="house_number",
 *                       type="string",
 *                       example="12"
 *                   ),
 *                   @OA\Property(
 *                       property="apartment_number",
 *                       type="string",
 *                       example="12"
 *                   ),
 *                   @OA\Property(
 *                       property="birth_date",
 *                       type="string",
 *                       format="date",
 *                       example="2000-01-01"
 *                   )
 *              )
 *          )
 *      ),
 *      @OA\Response(response="200", description="Login successful"),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthorized"
 *      )
 *  )
 * @OA\Put(
 *      path="/users/{userId}/update",
 *      tags={"Users"},
 *      security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *          name="Accept",
 *          in="header",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              default="application/json"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="userId",
 *          in="path",
 *          required=true,
 *          description="User ID to update",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\RequestBody(
 *            required=true,
 *            @OA\MediaType(
 *                mediaType="application/json",
 *                @OA\Schema(
 *                    type="object",
 *                    required={"last_name", "first_name"},
 *                    @OA\Property(
 *                        property="last_name",
 *                        type="string",
 *                        example="Lastname"
 *                    ),
 *                    @OA\Property(
 *                        property="first_name",
 *                        type="string",
 *                        example="Firstname"
 *                    ),
 *                    @OA\Property(
 *                        property="patronymic_name",
 *                        type="string",
 *                        example="Patronymicname"
 *                    ),
 *                    @OA\Property(
 *                        property="role",
 *                        ref="#/components/schemas/Role"
 *                    ),
 *                     @OA\Property(
 *                         property="email",
 *                         type="string",
 *                         example="email@example.com"
 *                     ),
 *                     @OA\Property(
 *                         property="city",
 *                         type="string",
 *                         example="ExampleCity"
 *                     ),
 *                     @OA\Property(
 *                         property="street",
 *                         type="string",
 *                         example="ExampleStreet"
 *                     ),
 *                     @OA\Property(
 *                         property="house_number",
 *                         type="string",
 *                         example="12"
 *                     ),
 *                     @OA\Property(
 *                         property="apartment_number",
 *                         type="string",
 *                         example="12"
 *                     ),
 *                     @OA\Property(
 *                         property="birth_date",
 *                         type="string",
 *                         format="date",
 *                         example="2000-01-01"
 *                     )
 *                )
 *            )
 *        ),
 *      @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *          @OA\JsonContent(ref="#/components/schemas/Profile")
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unathorized"
 *      ),
 *      @OA\Response(
 *          response=403,
 *          description="Bad request"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="User not found"
 *      )
 *  )
 * @OA\Delete(
 *      path="/users/{userId}/delete",
 *      tags={"Users"},
 *      security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *          name="Accept",
 *          in="header",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              default="application/json"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="userId",
 *          in="path",
 *          required=true,
 *          description="User ID to update",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *          @OA\JsonContent(ref="#/components/schemas/Profile")
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unathorized"
 *      ),
 *      @OA\Response(
 *          response=403,
 *          description="Bad request"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="User not found"
 *      )
 *  )
 * @OA\Get(
 *      path="/users/{userId}/reactivate",
 *      tags={"Users"},
 *      security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *          name="Accept",
 *          in="header",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              default="application/json"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="userId",
 *          in="path",
 *          required=true,
 *          description="User ID to update",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *          @OA\JsonContent(ref="#/components/schemas/Profile")
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unathorized"
 *      ),
 *      @OA\Response(
 *          response=403,
 *          description="Bad request"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="User not found"
 *      )
 *  )
 * @OA\Get(
 *      path="/users/{userId}/deactivate",
 *      tags={"Users"},
 *      security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *          name="Accept",
 *          in="header",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              default="application/json"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="userId",
 *          in="path",
 *          required=true,
 *          description="User ID to update",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *          @OA\JsonContent(ref="#/components/schemas/Profile")
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unathorized"
 *      ),
 *      @OA\Response(
 *          response=403,
 *          description="Bad request"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="User not found"
 *      )
 *  )
 * @OA\Get(
 *      path="/user/{userId}",
 *      tags={"Users"},
 *      security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *          name="Accept",
 *          in="header",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              default="application/json"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="userId",
 *          in="path",
 *          required=true,
 *          description="User ID to update",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *          @OA\JsonContent(ref="#/components/schemas/Profile")
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unathorized"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="User not found"
 *      )
 *  )
 * @OA\Get(
 *      path="/roles_list",
 *      tags={"Roles"},
 *      security={{"bearerAuth":{}}},
 *
 *      @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *          @OA\JsonContent(ref="#/components/schemas/Role")
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unathorized"
 *      )
 *  )
 * @OA\Get(
 *       path="/employees/active",
 *       tags={"Employees"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *      @OA\Parameter(
 *            name="page",
 *            in="query",
 *            description="The page number to retrieve.",
 *            required=false,
 *            @OA\Schema(
 *                type="integer",
 *                format="int32"
 *            )
 *       ),
 *       @OA\Response(
 *           response=200,
 *           description="Active employees list "
 *       ),
 *       @OA\Response(
 *           response=401,
 *           description="Error: Bad Request",
 *           @OA\JsonContent(
 *                type="object",
 *                @OA\Property(property="error", type="string", example="Error message")
 *           )
 *       ),
 *   )
 * @OA\Get(
 *       path="/employees/not_active",
 *       tags={"Employees"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *      @OA\Parameter(
 *            name="page",
 *            in="query",
 *            description="The page number to retrieve.",
 *            required=false,
 *            @OA\Schema(
 *                type="integer",
 *                format="int32"
 *            )
 *       ),
 *       @OA\Response(
 *           response=200,
 *           description="Not active employees list "
 *       ),
 *       @OA\Response(
 *           response=401,
 *           description="Error: Bad Request",
 *           @OA\JsonContent(
 *                type="object",
 *                @OA\Property(property="error", type="string", example="Error message")
 *           )
 *       ),
 *   )
 * @OA\Get(
 *       path="/employees/working",
 *       tags={"Employees"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *      @OA\Parameter(
 *            name="page",
 *            in="query",
 *            description="The page number to retrieve.",
 *            required=false,
 *            @OA\Schema(
 *                type="integer",
 *                format="int32"
 *            )
 *       ),
 *       @OA\Response(
 *           response=200,
 *           description="Not active employees list "
 *       ),
 *       @OA\Response(
 *           response=401,
 *           description="Error: Bad Request",
 *           @OA\JsonContent(
 *                type="object",
 *                @OA\Property(property="error", type="string", example="Error message")
 *           )
 *       ),
 *   )
 * @OA\Get(
 *       path="/employees/{employeeId}",
 *       tags={"Employees"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *       @OA\Parameter(
 *           name="employeeId",
 *           in="path",
 *           required=true,
 *           @OA\Schema(
 *               type="integer"
 *           )
 *       ),
 *       @OA\Response(
 *           response=200,
 *           description="Successful operation"
 *       ),
 *       @OA\Response(
 *           response=401,
 *           description="Unathorized"
 *       ),
 *       @OA\Response(
 *           response=404,
 *           description="User not found"
 *       )
 *   )
 * @OA\Get(
 *       path="/employees/{employeeId}/reactivate",
 *       tags={"Employees"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *       @OA\Parameter(
 *           name="employeeId",
 *           in="path",
 *           required=true,
 *           @OA\Schema(
 *               type="integer"
 *           )
 *       ),
 *       @OA\Response(
 *           response=200,
 *           description="Successful operation"
 *       ),
 *       @OA\Response(
 *           response=401,
 *           description="Unathorized"
 *       ),
 *       @OA\Response(
 *           response=403,
 *           description="Bad request"
 *       ),
 *       @OA\Response(
 *           response=404,
 *           description="User not found"
 *       )
 *   )
 * @OA\Get(
 *       path="/employees/{employeeId}/deactivate",
 *       tags={"Employees"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *       @OA\Parameter(
 *           name="employeeId",
 *           in="path",
 *           required=true,
 *           description="User ID to update",
 *           @OA\Schema(
 *               type="integer"
 *           )
 *       ),
 *       @OA\Response(
 *           response=200,
 *           description="Successful operation"
 *       ),
 *       @OA\Response(
 *           response=401,
 *           description="Unathorized"
 *       ),
 *       @OA\Response(
 *           response=403,
 *           description="Bad request"
 *       ),
 *       @OA\Response(
 *           response=404,
 *           description="User not found"
 *       )
 *   )
 * @OA\Post(
 *       path="/employees/create",
 *       tags={"Employees"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *       @OA\RequestBody(
 *           required=true,
 *           @OA\MediaType(
 *               mediaType="application/json",
 *               @OA\Schema(
 *                   type="object",
 *                   required={"last_name", "first_name"},
 *                   @OA\Property(
 *                        property="user",
 *                        type="object",
 *                        required={"last_name", "first_name"},
 *                         @OA\Property(
 *                             property="last_name",
 *                             type="string",
 *                             example="Lastname"
 *                         ),
 *                         @OA\Property(
 *                             property="first_name",
 *                             type="string",
 *                             example="Firstname"
 *                         ),
 *                         @OA\Property(
 *                             property="patronymic_name",
 *                             type="string",
 *                             example="Patronymicname"
 *                         ),
 *                          @OA\Property(
 *                              property="email",
 *                              type="string",
 *                              example="email@example.com"
 *                          ),
 *                          @OA\Property(
 *                              property="city",
 *                              type="string",
 *                              example="ExampleCity"
 *                          ),
 *                          @OA\Property(
 *                              property="street",
 *                              type="string",
 *                              example="ExampleStreet"
 *                          ),
 *                          @OA\Property(
 *                              property="house_number",
 *                              type="string",
 *                              example="12"
 *                          ),
 *                          @OA\Property(
 *                              property="apartment_number",
 *                              type="string",
 *                              example="12"
 *                          ),
 *                          @OA\Property(
 *                              property="birth_date",
 *                              type="string",
 *                              format="date",
 *                              example="2000-01-01"
 *                          )
 *                    ),
 *                   @OA\Property(
 *                        property="employee",
 *                        type="object",
 *                        required={"position_id"},
 *                         @OA\Property(
 *                             property="position_id",
 *                             type="number",
 *                             example="1"
 *                         ),
 *                         @OA\Property(
 *                             property="phone",
 *                             type="string",
 *                             example="+380256456"
 *                         ),
 *                         @OA\Property(
 *                             property="contract_number",
 *                             type="string",
 *                             example="123456"
 *                         ),
 *                          @OA\Property(
 *                              property="passport_data",
 *                              type="string"
 *                          ),
 *                          @OA\Property(
 *                              property="bank_account",
 *                              type="string"
 *                          ),
 *                          @OA\Property(
 *                              property="bank_title",
 *                              type="string"
 *                          ),
 *                          @OA\Property(
 *                              property="EDRPOU_bank_code",
 *                              type="string"
 *                          ),
 *                          @OA\Property(
 *                              property="code_IBAN",
 *                              type="string"
 *                          ),
 *                          @OA\Property(
 *                              property="medical_card_number",
 *                              type="string"
 *                          ),
 *                          @OA\Property(
 *                              property="employment_date",
 *                              type="string",
 *                              format="date",
 *                              example="2000-01-01"
 *                          )
 *                    )
 *               )
 *           )
 *       ),
 *       @OA\Response(response="200", description="Successful operation"),
 *       @OA\Response(
 *           response=401,
 *           description="Unauthorized"
 *       )
 *   )
 * @OA\Put(
 *       path="/employees/{employeeId}/update",
 *       tags={"Employees"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *     @OA\Parameter(
 *           name="employeeId",
 *           in="path",
 *           required=true,
 *           description="User ID to update",
 *           @OA\Schema(
 *               type="integer"
 *           )
 *       ),
 *       @OA\RequestBody(
 *           required=true,
 *           @OA\MediaType(
 *               mediaType="application/json",
 *               @OA\Schema(
 *                   type="object",
 *                   required={"last_name", "first_name"},
 *                   @OA\Property(
 *                        property="user",
 *                        type="object",
 *                        required={"last_name", "first_name"},
 *                         @OA\Property(
 *                             property="last_name",
 *                             type="string",
 *                             example="Lastname"
 *                         ),
 *                         @OA\Property(
 *                             property="first_name",
 *                             type="string",
 *                             example="Firstname"
 *                         ),
 *                         @OA\Property(
 *                             property="patronymic_name",
 *                             type="string",
 *                             example="Patronymicname"
 *                         ),
 *                          @OA\Property(
 *                              property="email",
 *                              type="string",
 *                              example="email@example.com"
 *                          ),
 *                          @OA\Property(
 *                              property="city",
 *                              type="string",
 *                              example="ExampleCity"
 *                          ),
 *                          @OA\Property(
 *                              property="street",
 *                              type="string",
 *                              example="ExampleStreet"
 *                          ),
 *                          @OA\Property(
 *                              property="house_number",
 *                              type="string",
 *                              example="12"
 *                          ),
 *                          @OA\Property(
 *                              property="apartment_number",
 *                              type="string",
 *                              example="12"
 *                          ),
 *                          @OA\Property(
 *                              property="birth_date",
 *                              type="string",
 *                              format="date",
 *                              example="2000-01-01"
 *                          )
 *                    ),
 *                   @OA\Property(
 *                        property="employee",
 *                        type="object",
 *                        required={"position_id"},
 *                         @OA\Property(
 *                             property="position_id",
 *                             type="number",
 *                             example="1"
 *                         ),
 *                         @OA\Property(
 *                             property="phone",
 *                             type="string",
 *                             example="+380256456"
 *                         ),
 *                         @OA\Property(
 *                             property="contract_number",
 *                             type="string",
 *                             example="123456"
 *                         ),
 *                          @OA\Property(
 *                              property="passport_data",
 *                              type="string"
 *                          ),
 *                          @OA\Property(
 *                              property="bank_account",
 *                              type="string"
 *                          ),
 *                          @OA\Property(
 *                              property="bank_title",
 *                              type="string"
 *                          ),
 *                          @OA\Property(
 *                              property="EDRPOU_bank_code",
 *                              type="string"
 *                          ),
 *                          @OA\Property(
 *                              property="code_IBAN",
 *                              type="string"
 *                          ),
 *                          @OA\Property(
 *                              property="medical_card_number",
 *                              type="string"
 *                          ),
 *                          @OA\Property(
 *                              property="employment_date",
 *                              type="string",
 *                              format="date",
 *                              example="2000-01-01"
 *                          )
 *                    )
 *               )
 *           )
 *       ),
 *       @OA\Response(response="200", description="Successful operation"),
 *       @OA\Response(
 *           response=401,
 *           description="Unauthorized"
 *       )
 *   )
 * @OA\Post(
 *       path="/employees/{employeeId}/fire-employee",
 *       tags={"Employees"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *     @OA\Parameter(
 *           name="employeeId",
 *           in="path",
 *           required=true,
 *           description="User ID to update",
 *           @OA\Schema(
 *               type="integer"
 *           )
 *       ),
 *       @OA\RequestBody(
 *           required=true,
 *           @OA\MediaType(
 *               mediaType="application/json",
 *               @OA\Schema(
 *                   type="object",
 *                   required={"last_name", "first_name"},
 *                   @OA\Property(
 *                        property="date_dismissal",
 *                        type="string",
 *                        format="date",
 *                        example="2024-06-16"
 *                    )
 *               )
 *           )
 *       ),
 *       @OA\Response(response="200", description="Successful operation"),
 *       @OA\Response(
 *           response=401,
 *           description="Unauthorized"
 *       ),
 *       @OA\Response(
 *           response=403,
 *           description="Bad request"
 *       )
 *   )
 * @OA\Delete(
 *       path="/employees/{employeeId}/delete",
 *       tags={"Employees"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *       @OA\Parameter(
 *           name="employeeId",
 *           in="path",
 *           required=true,
 *           description="Employee ID to delete",
 *           @OA\Schema(
 *               type="integer"
 *           )
 *       ),
 *       @OA\Response(
 *           response=200,
 *           description="Successful operation"
 *       ),
 *       @OA\Response(
 *           response=401,
 *           description="Unathorized"
 *       ),
 *       @OA\Response(
 *           response=403,
 *           description="Bad request"
 *       ),
 *       @OA\Response(
 *           response=404,
 *           description="User not found"
 *       )
 *   )
 * @OA\Get(
 *       path="/parrents/for-select",
 *       tags={"Parents"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *       @OA\Response(
 *           response=200,
 *           description="Not active parents list "
 *       ),
 *       @OA\Response(
 *           response=401,
 *           description="Error: Bad Request",
 *           @OA\JsonContent(
 *                type="object",
 *                @OA\Property(property="error", type="string", example="Error message")
 *           )
 *       ),
 *   )
 * @OA\Get(
 *       path="/parrents/active",
 *       tags={"Parents"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *      @OA\Parameter(
 *            name="page",
 *            in="query",
 *            description="The page number to retrieve.",
 *            required=false,
 *            @OA\Schema(
 *                type="integer",
 *                format="int32"
 *            )
 *       ),
 *       @OA\Response(
 *           response=200,
 *           description="Successful operation"
 *       ),
 *       @OA\Response(
 *           response=401,
 *           description="Error: Bad Request",
 *           @OA\JsonContent(
 *                type="object",
 *                @OA\Property(property="error", type="string", example="Error message")
 *           )
 *       ),
 *   )
 * @OA\Get(
 *       path="/parrents/not-active",
 *       tags={"Parents"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *      @OA\Parameter(
 *            name="page",
 *            in="query",
 *            description="The page number to retrieve.",
 *            required=false,
 *            @OA\Schema(
 *                type="integer",
 *                format="int32"
 *            )
 *       ),
 *       @OA\Response(
 *           response=200,
 *           description="Successful operation"
 *       ),
 *       @OA\Response(
 *           response=401,
 *           description="Error: Bad Request",
 *           @OA\JsonContent(
 *                type="object",
 *                @OA\Property(property="error", type="string", example="Error message")
 *           )
 *       ),
 *   )
 * @OA\Get(
 *       path="/parrents/{parrentId}",
 *       tags={"Parents"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *       @OA\Parameter(
 *           name="parrentId",
 *           in="path",
 *           required=true,
 *           @OA\Schema(
 *               type="integer"
 *           )
 *       ),
 *       @OA\Response(
 *           response=200,
 *           description="Successful operation"
 *       ),
 *       @OA\Response(
 *           response=401,
 *           description="Unathorized"
 *       ),
 *       @OA\Response(
 *           response=404,
 *           description="Parent not found"
 *       )
 *   )
 * @OA\Get(
 *       path="/parrents/{parrentId}/reactivate",
 *       tags={"Parents"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *       @OA\Parameter(
 *           name="parrentId",
 *           in="path",
 *           required=true,
 *           @OA\Schema(
 *               type="integer"
 *           )
 *       ),
 *       @OA\Response(
 *           response=200,
 *           description="Successful operation"
 *       ),
 *       @OA\Response(
 *           response=401,
 *           description="Unathorized"
 *       ),
 *       @OA\Response(
 *           response=403,
 *           description="Bad request"
 *       ),
 *       @OA\Response(
 *           response=404,
 *           description="User not found"
 *       )
 *   )
 * @OA\Get(
 *       path="/parrents/{parrentId}/deactivate",
 *       tags={"Parents"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *       @OA\Parameter(
 *           name="parrentId",
 *           in="path",
 *           required=true,
 *           @OA\Schema(
 *               type="integer"
 *           )
 *       ),
 *       @OA\Response(
 *           response=200,
 *           description="Successful operation"
 *       ),
 *       @OA\Response(
 *           response=401,
 *           description="Unathorized"
 *       ),
 *       @OA\Response(
 *           response=403,
 *           description="Bad request"
 *       ),
 *       @OA\Response(
 *           response=404,
 *           description="User not found"
 *       )
 *   )
 * @OA\Post(
 *     path="/parrents/create",
 *     tags={"Parents"},
 *     security={{"bearerAuth":{}}},
 *    @OA\Parameter(
 *         name="Accept",
 *         in="header",
 *         required=true,
 *         @OA\Schema(
 *             type="string",
 *             default="application/json"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 schema="UserParentSchema",
 *                 type="object",
 *                 @OA\Property(
 *                     property="user",
 *                     type="object",
 *                     @OA\Property(property="last_name", type="string", description="User's last name"),
 *                     @OA\Property(property="first_name", type="string", description="User's first name"),
 *                     @OA\Property(property="patronymic_name", type="string", description="User's patronymic name"),
 *                     @OA\Property(property="email", type="string", format="email", description="User's email"),
 *                     @OA\Property(property="city", type="string", description="User's city"),
 *                     @OA\Property(property="street", type="string", description="User's street"),
 *                     @OA\Property(property="house_number", type="string", description="User's house number"),
 *                     @OA\Property(property="apartment_number", type="string", description="User's apartment number"),
 *                     @OA\Property(property="birth_date", type="string", format="date", description="User's birthdate")
 *                 ),
 *                 @OA\Property(
 *                     property="parrent",
 *                     type="object",
 *                     @OA\Property(property="phone", type="string", description="Parent's phone"),
 *                     @OA\Property(property="work_place", type="string", description="Parent's working place"),
 *                     @OA\Property(property="passport_data", type="string", description="Parent's passport data"),
 *                     @OA\Property(property="marital_status", type="string", description="Parent's marital status"),
 *                     @OA\Property(
 *                       property="children",
 *                       type="array",
 *                       @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="child_id", type="integer", description="Child's id"),
 *                         @OA\Property(property="relations", type="string", description="Relations")
 *                       )
 *                     )
 *                  )
 *              )
 *         )
 *     ),
 *     @OA\Response(response="200", description="Successful operation"),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 * @OA\Put(
 *       path="/parrents/{parrentId}/update",
 *       tags={"Parents"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *     @OA\Parameter(
 *           name="parrentId",
 *           in="path",
 *           required=true,
 *           description="User ID to update",
 *           @OA\Schema(
 *               type="integer"
 *           )
 *       ),
 *       @OA\RequestBody(
 *           required=true,
 *         @OA\MediaType(
 *               mediaType="application/json",
 *               @OA\Schema(
 *                   schema="UserParentSchema",
 *                   type="object",
 *                   @OA\Property(
 *                       property="user",
 *                       type="object",
 *                       @OA\Property(property="last_name", type="string", description="User's last name"),
 *                       @OA\Property(property="first_name", type="string", description="User's first name"),
 *                       @OA\Property(property="patronymic_name", type="string", description="User's patronymic name"),
 *                       @OA\Property(property="email", type="string", format="email", description="User's email"),
 *                       @OA\Property(property="city", type="string", description="User's city"),
 *                       @OA\Property(property="street", type="string", description="User's street"),
 *                       @OA\Property(property="house_number", type="string", description="User's house number"),
 *                       @OA\Property(property="apartment_number", type="string", description="User's apartment number"),
 *                       @OA\Property(property="birth_date", type="string", format="date", description="User's birthdate")
 *                   ),
 *                   @OA\Property(
 *                       property="parrent",
 *                       type="object",
 *                       @OA\Property(property="phone", type="string", description="Parent's phone"),
 *                       @OA\Property(property="work_place", type="string", description="Parent's working place"),
 *                       @OA\Property(property="passport_data", type="string", description="Parent's passport data"),
 *                       @OA\Property(property="marital_status", type="string", description="Parent's marital status"),
 *                       @OA\Property(
 *                         property="children",
 *                         type="array",
 *                         @OA\Items(
 *                           type="object",
 *                           @OA\Property(property="child_id", type="integer", description="Child's id"),
 *                           @OA\Property(property="relations", type="string", description="Relations")
 *                         )
 *                       )
 *                    )
 *                )
 *           )
 *       ),
 *       @OA\Response(response="200", description="Successful operation"),
 *       @OA\Response(
 *           response=401,
 *           description="Unauthorized"
 *       )
 *   )
 *  @OA\Delete(
 *        path="/parrents/{parrentId}/delete",
 *        tags={"Parents"},
 *        security={{"bearerAuth":{}}},
 *       @OA\Parameter(
 *            name="Accept",
 *            in="header",
 *            required=true,
 *            @OA\Schema(
 *                type="string",
 *                default="application/json"
 *            )
 *        ),
 *        @OA\Parameter(
 *            name="parrentId",
 *            in="path",
 *            required=true,
 *            description="Employee ID to delete",
 *            @OA\Schema(
 *                type="integer"
 *            )
 *        ),
 *        @OA\Response(
 *            response=200,
 *            description="Successful operation"
 *        ),
 *        @OA\Response(
 *            response=401,
 *            description="Unathorized"
 *        ),
 *        @OA\Response(
 *            response=403,
 *            description="Bad request"
 *        ),
 *        @OA\Response(
 *            response=404,
 *            description="User not found"
 *        )
 *    )
 * @OA\Get(
 *       path="/positions",
 *       tags={"Positions"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *
 *       @OA\Response(
 *           response=200,
 *           description="Successful operation"
 *       ),
 *       @OA\Response(
 *           response=401,
 *           description="Unathorized"
 *       )
 *   )
 * @OA\Get(
 *       path="/positions/{positionId}",
 *       tags={"Positions"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *       @OA\Parameter(
 *           name="positionId",
 *           in="path",
 *           required=true,
 *           description="User ID to update",
 *           @OA\Schema(
 *               type="integer"
 *           )
 *       ),
 *       @OA\Response(
 *           response=200,
 *           description="Successful operation"
 *       ),
 *       @OA\Response(
 *           response=401,
 *           description="Unathorized"
 *       )
 *   )
 * @OA\Post(
 *       path="/positions/create",
 *       tags={"Positions"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *       @OA\RequestBody(
 *           required=true,
 *           @OA\MediaType(
 *               mediaType="application/json",
 *               @OA\Schema(
 *                   type="object",
 *                   required={"position_title"},
 *                   @OA\Property(
 *                        property="position_title",
 *                        type="string"
 *                    )
 *               )
 *           )
 *       ),
 *       @OA\Response(response="200", description="Successful operation"),
 *       @OA\Response(
 *           response=401,
 *           description="Unauthorized"
 *       )
 *   )
 * @OA\Put(
 *       path="/positions/{positionId}/update",
 *       tags={"Positions"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *     @OA\Parameter(
 *           name="positionId",
 *           in="path",
 *           required=true,
 *           description="User ID to update",
 *           @OA\Schema(
 *               type="integer"
 *           )
 *       ),
 *       @OA\RequestBody(
 *           required=true,
 *           @OA\MediaType(
 *               mediaType="application/json",
 *               @OA\Schema(
 *                   type="object",
 *                   required={"position_title"},
 *                   @OA\Property(
 *                        property="position_title",
 *                        type="string"
 *                    )
 *               )
 *           )
 *       ),
 *       @OA\Response(response="200", description="Successful operation"),
 *       @OA\Response(
 *           response=401,
 *           description="Unauthorized"
 *       )
 *   )
 * @OA\Delete(
 *       path="/positions/{positionId}/delete",
 *       tags={"Positions"},
 *       security={{"bearerAuth":{}}},
 *      @OA\Parameter(
 *           name="Accept",
 *           in="header",
 *           required=true,
 *           @OA\Schema(
 *               type="string",
 *               default="application/json"
 *           )
 *       ),
 *       @OA\Parameter(
 *           name="positionId",
 *           in="path",
 *           required=true,
 *           description="Employee ID to delete",
 *           @OA\Schema(
 *               type="integer"
 *           )
 *       ),
 *       @OA\Response(
 *           response=200,
 *           description="Successful operation"
 *       ),
 *       @OA\Response(
 *           response=401,
 *           description="Unathorized"
 *       )
 *   )
 * @OA\Get(
 *        path="/children/for-select",
 *        tags={"Children"},
 *        security={{"bearerAuth":{}}},
 *       @OA\Parameter(
 *            name="Accept",
 *            in="header",
 *            required=true,
 *            @OA\Schema(
 *                type="string",
 *                default="application/json"
 *            )
 *        ),
 *        @OA\Response(
 *            response=200,
 *            description="Successful operation"
 *        ),
 *        @OA\Response(
 *            response=401,
 *            description="Error: Bad Request",
 *            @OA\JsonContent(
 *                 type="object",
 *                 @OA\Property(property="error", type="string", example="Error message")
 *            )
 *        ),
 *    )
 * @OA\Get(
 *        path="/children/{parrentId}",
 *        tags={"Children"},
 *        security={{"bearerAuth":{}}},
 *       @OA\Parameter(
 *            name="Accept",
 *            in="header",
 *            required=true,
 *            @OA\Schema(
 *                type="string",
 *                default="application/json"
 *            )
 *        ),
 *        @OA\Parameter(
 *            name="parrentId",
 *            in="path",
 *            required=true,
 *            description="User ID to update",
 *            @OA\Schema(
 *                type="integer"
 *            )
 *        ),
 *        @OA\Response(
 *            response=200,
 *            description="Successful operation"
 *        ),
 *        @OA\Response(
 *            response=401,
 *            description="Unathorized"
 *        )
 *    )
 * @OA\Get(
 *        path="/children/all",
 *        tags={"Children"},
 *        security={{"bearerAuth":{}}},
 *       @OA\Parameter(
 *            name="Accept",
 *            in="header",
 *            required=true,
 *            @OA\Schema(
 *                type="string",
 *                default="application/json"
 *            )
 *        ),
 *        @OA\Response(
 *            response=200,
 *            description="Successful operation"
 *        ),
 *        @OA\Response(
 *            response=401,
 *            description="Unathorized"
 *        )
 *    )
 * @OA\Get(
 *        path="/children/for-enrolment",
 *        tags={"Children"},
 *        security={{"bearerAuth":{}}},
 *       @OA\Parameter(
 *            name="Accept",
 *            in="header",
 *            required=true,
 *            @OA\Schema(
 *                type="string",
 *                default="application/json"
 *            )
 *        ),
 *        @OA\Response(
 *            response=200,
 *            description="Successful operation"
 *        ),
 *        @OA\Response(
 *            response=401,
 *            description="Unathorized"
 *        )
 *    )
 * @OA\Get(
 *        path="/children/in-training",
 *        tags={"Children"},
 *        security={{"bearerAuth":{}}},
 *       @OA\Parameter(
 *            name="Accept",
 *            in="header",
 *            required=true,
 *            @OA\Schema(
 *                type="string",
 *                default="application/json"
 *            )
 *        ),
 *        @OA\Response(
 *            response=200,
 *            description="Successful operation"
 *        ),
 *        @OA\Response(
 *            response=401,
 *            description="Unathorized"
 *        )
 *    )
 * @OA\Get(
 *        path="/children/graduated",
 *        tags={"Children"},
 *        security={{"bearerAuth":{}}},
 *       @OA\Parameter(
 *            name="Accept",
 *            in="header",
 *            required=true,
 *            @OA\Schema(
 *                type="string",
 *                default="application/json"
 *            )
 *        ),
 *        @OA\Response(
 *            response=200,
 *            description="Successful operation"
 *        ),
 *        @OA\Response(
 *            response=401,
 *            description="Unathorized"
 *        )
 *    )
 * @OA\Get(
 *        path="/children/{childId}",
 *        tags={"Children"},
 *        security={{"bearerAuth":{}}},
 *       @OA\Parameter(
 *            name="Accept",
 *            in="header",
 *            required=true,
 *            @OA\Schema(
 *                type="string",
 *                default="application/json"
 *            )
 *        ),
 *        @OA\Parameter(
 *            name="childId",
 *            in="path",
 *            required=true,
 *            @OA\Schema(
 *                type="integer"
 *            )
 *        ),
 *        @OA\Response(
 *            response=200,
 *            description="Successful operation"
 *        ),
 *        @OA\Response(
 *            response=401,
 *            description="Unathorized"
 *        ),
 *        @OA\Response(
 *            response=403,
 *            description="Bad request"
 *        ),
 *        @OA\Response(
 *            response=404,
 *            description="User not found"
 *        )
 *    )
 * @OA\Post(
 *      path="/children/create",
 *      tags={"Children"},
 *      security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *          name="Accept",
 *          in="header",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              default="application/json"
 *          )
 *      ),
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(
 *                  schema="UserChildSchema",
 *                  type="object",
 *                  @OA\Property(
 *                      property="user",
 *                      type="object",
 *                      @OA\Property(property="last_name", type="string", description="User's last name"),
 *                      @OA\Property(property="first_name", type="string", description="User's first name"),
 *                      @OA\Property(property="patronymic_name", type="string", description="User's patronymic name"),
 *                      @OA\Property(property="email", type="string", format="email", description="User's email"),
 *                      @OA\Property(property="city", type="string", description="User's city"),
 *                      @OA\Property(property="street", type="string", description="User's street"),
 *                      @OA\Property(property="house_number", type="string", description="User's house number"),
 *                      @OA\Property(property="apartment_number", type="string", description="User's apartment number"),
 *                      @OA\Property(property="birth_date", type="string", format="date", description="User's birthdate")
 *                  ),
 *                  @OA\Property(
 *                      property="child",
 *                      type="object",
 *                      @OA\Property(property="group_id", type="integer"),
 *                      @OA\Property(property="mental_helth", type="string"),
 *                      @OA\Property(property="birth_certificate", type="string"),
 *                      @OA\Property(property="medical_card_number", type="string"),
 *                      @OA\Property(property="social_status", type="string"),
 *                      @OA\Property(property="enrollment_date", type="string"),
 *                      @OA\Property(
 *                        property="parrents",
 *                        type="array",
 *                        @OA\Items(
 *                          type="object",
 *                          @OA\Property(property="parrent_id", type="integer", description="Child's id"),
 *                          @OA\Property(property="relations", type="string", description="Relations")
 *                        )
 *                      )
 *                   )
 *               )
 *          )
 *      ),
 *      @OA\Response(response="200", description="Successful operation"),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthorized"
 *      )
 *  )
 * @OA\Put(
 *        path="/children/{childId}/update",
 *        tags={"Children"},
 *        security={{"bearerAuth":{}}},
 *       @OA\Parameter(
 *            name="Accept",
 *            in="header",
 *            required=true,
 *            @OA\Schema(
 *                type="string",
 *                default="application/json"
 *            )
 *        ),
 *      @OA\Parameter(
 *            name="childId",
 *            in="path",
 *            required=true,
 *            description="User ID to update",
 *            @OA\Schema(
 *                type="integer"
 *            )
 *        ),
 *       @OA\RequestBody(
 *           required=true,
 *           @OA\MediaType(
 *               mediaType="application/json",
 *               @OA\Schema(
 *                   schema="UserChildSchema",
 *                   type="object",
 *                   @OA\Property(
 *                       property="user",
 *                       type="object",
 *                       @OA\Property(property="last_name", type="string", description="User's last name"),
 *                       @OA\Property(property="first_name", type="string", description="User's first name"),
 *                       @OA\Property(property="patronymic_name", type="string", description="User's patronymic name"),
 *                       @OA\Property(property="email", type="string", format="email", description="User's email"),
 *                       @OA\Property(property="city", type="string", description="User's city"),
 *                       @OA\Property(property="street", type="string", description="User's street"),
 *                       @OA\Property(property="house_number", type="string", description="User's house number"),
 *                       @OA\Property(property="apartment_number", type="string", description="User's apartment number"),
 *                       @OA\Property(property="birth_date", type="string", format="date", description="User's birthdate")
 *                   ),
 *                   @OA\Property(
 *                       property="child",
 *                       type="object",
 *                       @OA\Property(property="group_id", type="integer"),
 *                       @OA\Property(property="mental_helth", type="string"),
 *                       @OA\Property(property="birth_certificate", type="string"),
 *                       @OA\Property(property="medical_card_number", type="string"),
 *                       @OA\Property(property="social_status", type="string"),
 *                       @OA\Property(property="enrollment_date", type="string"),
 *                       @OA\Property(property="graduation_date", type="string"),
 *                       @OA\Property(
 *                         property="parrents",
 *                         type="array",
 *                         @OA\Items(
 *                           type="object",
 *                           @OA\Property(property="parrent_id", type="integer", description="Child's id"),
 *                           @OA\Property(property="relations", type="string", description="Relations")
 *                         )
 *                       )
 *                    )
 *                )
 *           )
 *       ),
 *        @OA\Response(response="200", description="Successful operation"),
 *        @OA\Response(
 *            response=401,
 *            description="Unauthorized"
 *        )
 *    )
 * @OA\Delete(
 *         path="/children/{childId}/delete",
 *         tags={"Children"},
 *         security={{"bearerAuth":{}}},
 *        @OA\Parameter(
 *             name="Accept",
 *             in="header",
 *             required=true,
 *             @OA\Schema(
 *                 type="string",
 *                 default="application/json"
 *             )
 *         ),
 *         @OA\Parameter(
 *             name="childId",
 *             in="path",
 *             required=true,
 *             description="Employee ID to delete",
 *             @OA\Schema(
 *                 type="integer"
 *             )
 *         ),
 *         @OA\Response(
 *             response=200,
 *             description="Successful operation"
 *         ),
 *         @OA\Response(
 *             response=401,
 *             description="Unathorized"
 *         ),
 *         @OA\Response(
 *             response=403,
 *             description="Bad request"
 *         ),
 *         @OA\Response(
 *             response=404,
 *             description="User not found"
 *         )
 *     )
 */
class SwaggerAnnotationsController extends Controller
{

}
