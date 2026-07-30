<?php

namespace App\Http\Controllers\Api\Customers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\UserMemorizedAyah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MemorizedAyahController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/customers/memorized",
     *     tags={"Memorized Ayahs"},
     *     summary="Mark ayahs as memorized (bulk)",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(ref="#/components/parameters/Accept-Language"),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"ayahs"},
     *
     *             @OA\Property(
     *                 property="ayahs",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *                     required={"surah_id","ayah_number"},
     *
     *                     @OA\Property(property="surah_id", type="integer", example=2),
     *                     @OA\Property(property="ayah_number", type="integer", example=255),
     *                     @OA\Property(
     *                         property="statuses",
     *                         type="object",
     *                         nullable=true,
     *                         description="Map of status name to boolean flag (true to add/keep, false to remove, omitted to preserve existing state. If all statuses become false/empty, the record is deleted.)",
     *                         example={"memorized": true, "bookmarked": false, "saved": false}
     *                     ),
     *                     @OA\Property(property="status", type="string", nullable=true, example="memorized", description="Deprecated single status string for backward compatibility")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Ayahs memorized successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Ayahs memorized successfully"),
     *             @OA\Property(property="data", type="array",
     *
     *                 @OA\Items(type="object",
     *
     *                     @OA\Property(property="surah_id", type="integer", example=2),
     *                     @OA\Property(property="ayah_number", type="integer", example=255),
     *                     @OA\Property(property="memorized_at", type="string", format="date-time", example="2026-04-20T10:00:00.000000Z"),
     *                     @OA\Property(
     *                         property="statuses",
     *                         type="array",
     *                         nullable=true,
     *
     *                         @OA\Items(type="string"),
     *                         example={"memorized", "saved"}
     *                     )
     *                 )
     *             ),
     *
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
            'ayahs' => ['required', 'array', 'min:1'],
            'ayahs.*.surah_id' => ['required', 'integer', 'min:1'],
            'ayahs.*.ayah_number' => ['required', 'integer', 'min:1'],
            'ayahs.*.statuses' => ['nullable', 'array'],
            'ayahs.*.status' => ['nullable', 'string'],
        ]);

        $userId = $request->user()->id;
        $memorizedAt = Carbon::now();

        $existingRecords = UserMemorizedAyah::query()
            ->where('user_id', $userId)
            ->where(function ($query) use ($validated): void {
                foreach ($validated['ayahs'] as $ayah) {
                    $query->orWhere(fn ($q) => $q
                        ->where('surah_id', $ayah['surah_id'])
                        ->where('ayah_number', $ayah['ayah_number'])
                    );
                }
            })
            ->get()
            ->keyBy(fn ($item) => $item->surah_id.'_'.$item->ayah_number);

        $rowsToUpsert = [];
        $keysToDelete = [];

        foreach ($validated['ayahs'] as $ayah) {
            $key = $ayah['surah_id'].'_'.$ayah['ayah_number'];
            $existing = $existingRecords->get($key);
            $currentStatuses = $existing?->statuses ?? [];

            if (isset($ayah['statuses']) && is_array($ayah['statuses'])) {
                if (array_is_list($ayah['statuses'])) {
                    foreach ($ayah['statuses'] as $st) {
                        if (is_string($st) && ! in_array($st, $currentStatuses, true)) {
                            $currentStatuses[] = $st;
                        }
                    }
                } else {
                    foreach ($ayah['statuses'] as $statusKey => $flag) {
                        if ($flag) {
                            if (! in_array($statusKey, $currentStatuses, true)) {
                                $currentStatuses[] = $statusKey;
                            }
                        } else {
                            $currentStatuses = array_values(array_filter(
                                $currentStatuses,
                                fn ($st) => $st !== $statusKey
                            ));
                        }
                    }
                }
            } elseif (isset($ayah['status']) && $ayah['status'] !== null) {
                if (! in_array($ayah['status'], $currentStatuses, true)) {
                    $currentStatuses[] = $ayah['status'];
                }
            }

            $finalStatuses = array_values(array_unique($currentStatuses));

            if (empty($finalStatuses)) {
                $keysToDelete[] = [
                    'surah_id' => $ayah['surah_id'],
                    'ayah_number' => $ayah['ayah_number'],
                ];
            } else {
                $rowsToUpsert[] = [
                    'user_id' => $userId,
                    'surah_id' => $ayah['surah_id'],
                    'ayah_number' => $ayah['ayah_number'],
                    'memorized_at' => $memorizedAt,
                    'statuses' => json_encode($finalStatuses),
                ];
            }
        }

        if (! empty($keysToDelete)) {
            UserMemorizedAyah::query()
                ->where('user_id', $userId)
                ->where(function ($query) use ($keysToDelete): void {
                    foreach ($keysToDelete as $item) {
                        $query->orWhere(fn ($q) => $q
                            ->where('surah_id', $item['surah_id'])
                            ->where('ayah_number', $item['ayah_number'])
                        );
                    }
                })
                ->delete();
        }

        if (! empty($rowsToUpsert)) {
            UserMemorizedAyah::query()->upsert(
                $rowsToUpsert,
                uniqueBy: ['user_id', 'surah_id', 'ayah_number'],
                update: ['memorized_at', 'statuses'],
            );
        }

        $memorizedAyahs = UserMemorizedAyah::query()
            ->where('user_id', $userId)
            ->where(function ($query) use ($validated): void {
                foreach ($validated['ayahs'] as $ayah) {
                    $query->orWhere(fn ($q) => $q
                        ->where('surah_id', $ayah['surah_id'])
                        ->where('ayah_number', $ayah['ayah_number'])
                    );
                }
            })
            ->select(['surah_id', 'ayah_number', 'memorized_at', 'statuses'])
            ->get();

        return ApiResponse::success(
            data: $memorizedAyahs,
            message: __('messages.ayahs_memorized_successfully')
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/customers/memorized",
     *     tags={"Memorized Ayahs"},
     *     summary="Get all memorized ayahs",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(ref="#/components/parameters/Accept-Language"),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filter by status value within statuses array",
     *
     *         @OA\Schema(type="string", example="memorized")
     *     ),
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
     *                     @OA\Property(property="ayah_number", type="integer", example=1),
     *                     @OA\Property(property="statuses", type="array", @OA\Items(type="string"), example={"memorized", "bookmarked", "saved"})
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
        $request->validate([
            'status' => ['nullable', 'string'],
        ]);

        $memorizedAyahs = UserMemorizedAyah::query()
            ->where('user_id', $request->user()->id)
            ->when($request->filled('status'), fn ($query) => $query->whereJsonContains('statuses', $request->input('status')))
            ->select(['surah_id', 'ayah_number', 'statuses', 'updated_at'])
            ->orderBy('surah_id')
            ->orderBy('ayah_number')
            ->get();

        return ApiResponse::success(
            data: $memorizedAyahs,
            message: __('messages.memorized_ayahs_fetched_successfully')
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/customers/memorized/last",
     *     tags={"Memorized Ayahs"},
     *     summary="Get last memorized ayah",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(ref="#/components/parameters/Accept-Language"),
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
            message: __('messages.last_memorized_ayah_fetched_successfully')
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/customers/memorized/summary",
     *     tags={"Memorized Ayahs"},
     *     summary="Get memorized ayahs summary by surah",
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(ref="#/components/parameters/Accept-Language"),
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
            message: __('messages.memorized_ayahs_summary_fetched_successfully')
        );
    }
}
