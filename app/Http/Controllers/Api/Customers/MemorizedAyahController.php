<?php

namespace App\Http\Controllers\Api\Customers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\UserMemorizedAyah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemorizedAyahController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/customers/memorized",
     *     tags={"Memorized Ayahs"},
     *     summary="Mark ayah as memorized",
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"surah_id","ayah_number"},
     *
     *             @OA\Property(property="surah_id", type="integer", example=2),
     *             @OA\Property(property="ayah_number", type="integer", example=255)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Ayah memorized successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Ayah memorized successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="surah_id", type="integer", example=2),
     *                 @OA\Property(property="ayah_number", type="integer", example=255),
     *                 @OA\Property(property="memorized_at", type="string", format="date-time", example="2026-04-20T10:00:00.000000Z")
     *             ),
     *             @OA\Property(property="errors", type="null", example=null)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'surah_id' => ['required', 'integer', 'min:1'],
            'ayah_number' => ['required', 'integer', 'min:1'],
        ]);

        $memorizedAyah = UserMemorizedAyah::query()->updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'surah_id' => $validated['surah_id'],
                'ayah_number' => $validated['ayah_number'],
            ],
            [
                'memorized_at' => now(),
            ]
        );

        return ApiResponse::success(
            data: [
                'surah_id' => $memorizedAyah->surah_id,
                'ayah_number' => $memorizedAyah->ayah_number,
                'memorized_at' => $memorizedAyah->memorized_at,
            ],
            message: 'Ayah memorized successfully'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/customers/memorized",
     *     tags={"Memorized Ayahs"},
     *     summary="Get all memorized ayahs",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Memorized ayahs fetched successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Memorized ayahs fetched successfully"),
     *             @OA\Property(property="data", type="array",
     *
     *                 @OA\Items(type="object",
     *
     *                     @OA\Property(property="surah_id", type="integer", example=1),
     *                     @OA\Property(property="ayah_number", type="integer", example=1)
     *                 )
     *             ),
     *             @OA\Property(property="errors", type="null", example=null)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $memorizedAyahs = UserMemorizedAyah::query()
            ->where('user_id', $request->user()->id)
            ->select(['surah_id', 'ayah_number'])
            ->orderBy('surah_id')
            ->orderBy('ayah_number')
            ->get();

        return ApiResponse::success(
            data: $memorizedAyahs,
            message: 'Memorized ayahs fetched successfully'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/customers/memorized/last",
     *     tags={"Memorized Ayahs"},
     *     summary="Get last memorized ayah",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Last memorized ayah fetched successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Last memorized ayah fetched successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="surah_id", type="integer", example=2),
     *                 @OA\Property(property="ayah_number", type="integer", example=255),
     *                 @OA\Property(property="memorized_at", type="string", format="date-time", example="2026-04-20T10:00:00.000000Z")
     *             ),
     *             @OA\Property(property="errors", type="null", example=null)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function last(Request $request): JsonResponse
    {
        $lastMemorizedAyah = UserMemorizedAyah::query()
            ->where('user_id', $request->user()->id)
            ->select(['surah_id', 'ayah_number', 'memorized_at'])
            ->orderByDesc('memorized_at')
            ->orderByDesc('id')
            ->first();

        return ApiResponse::success(
            data: $lastMemorizedAyah,
            message: 'Last memorized ayah fetched successfully'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/customers/memorized/summary",
     *     tags={"Memorized Ayahs"},
     *     summary="Get memorized ayahs summary by surah",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Memorized summary fetched successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Memorized summary fetched successfully"),
     *             @OA\Property(property="data", type="array",
     *
     *                 @OA\Items(type="object",
     *
     *                     @OA\Property(property="surah_id", type="integer", example=2),
     *                     @OA\Property(property="memorized_count", type="integer", example=40),
     *                     @OA\Property(property="total_ayahs", type="integer", example=286),
     *                     @OA\Property(property="memorized_percentage", type="number", format="float", example=13.99)
     *                 )
     *             ),
     *             @OA\Property(property="errors", type="null", example=null)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function summary(Request $request): JsonResponse
    {
        $summary = UserMemorizedAyah::query()
            ->where('user_memorized_ayahs.user_id', $request->user()->id)
            ->join('surahs', 'surahs.surah_id', '=', 'user_memorized_ayahs.surah_id')
            ->groupBy('user_memorized_ayahs.surah_id', 'surahs.total_ayahs')
            ->selectRaw('user_memorized_ayahs.surah_id')
            ->selectRaw('COUNT(*) as memorized_count')
            ->selectRaw('surahs.total_ayahs')
            ->selectRaw('ROUND((COUNT(*) / surahs.total_ayahs) * 100, 2) as memorized_percentage')
            ->orderBy('user_memorized_ayahs.surah_id')
            ->get();

        return ApiResponse::success(
            data: $summary,
            message: 'Memorized summary fetched successfully'
        );
    }
}
