<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuranRecitationAyahResource;
use App\Http\Resources\QuranRecitationResource;
use App\Http\Responses\ApiResponse;
use App\Models\QuranRecitation;
use App\Models\QuranRecitationAyah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuranRecitationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/quran/recitations",
     *     tags={"Quran Recitations"},
     *     summary="List available Quran recitations",
     *
     *     @OA\Parameter(ref="#/components/parameters/Accept-Language"),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Recitations fetched successfully"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $recitations = QuranRecitation::query()
            ->withCount('ayahs')
            ->orderBy('name')
            ->get();

        return ApiResponse::success(
            message: __('messages.quran_recitations_fetched_successfully'),
            data: QuranRecitationResource::collection($recitations),
            status: 200,
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/quran/recitations/{slug}",
     *     tags={"Quran Recitations"},
     *     summary="Get a Quran recitation by slug",
     *
     *     @OA\Parameter(ref="#/components/parameters/Accept-Language"),
     *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string")),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Recitation fetched successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Recitation not found"
     *     )
     * )
     */
    public function show(string $slug): JsonResponse
    {
        $recitation = QuranRecitation::query()
            ->where('slug', $slug)
            ->withCount('ayahs')
            ->first();

        if ($recitation === null) {
            return ApiResponse::error(
                message: __('messages.quran_recitation_not_found'),
                status: 404,
            );
        }

        return ApiResponse::success(
            message: __('messages.quran_recitation_fetched_successfully'),
            data: new QuranRecitationResource($recitation),
            status: 200,
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/quran/recitations/{slug}/ayahs",
     *     tags={"Quran Recitations"},
     *     summary="List ayah audio files for a recitation",
     *
     *     @OA\Parameter(ref="#/components/parameters/Accept-Language"),
     *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Parameter(name="surah", in="query", required=false, @OA\Schema(type="integer")),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Recitation ayahs fetched successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Recitation not found"
     *     )
     * )
     */
    public function ayahs(Request $request, string $slug): JsonResponse
    {
        $recitation = QuranRecitation::query()->where('slug', $slug)->first();

        if ($recitation === null) {
            return ApiResponse::error(
                message: __('messages.quran_recitation_not_found'),
                status: 404,
            );
        }

        $query = QuranRecitationAyah::query()
            ->where('quran_recitation_id', $recitation->id)
            ->orderBy('surah')
            ->orderBy('ayah');

        if ($request->filled('surah')) {
            $query->where('surah', (int) $request->query('surah'));
        }

        return ApiResponse::success(
            message: __('messages.quran_recitation_ayahs_fetched_successfully'),
            data: QuranRecitationAyahResource::collection($query->get()),
            status: 200,
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/quran/recitations/{slug}/ayahs/{surah}/{ayah}",
     *     tags={"Quran Recitations"},
     *     summary="Get a single ayah audio file for a recitation",
     *
     *     @OA\Parameter(ref="#/components/parameters/Accept-Language"),
     *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Parameter(name="surah", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="ayah", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Recitation ayah fetched successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Recitation or ayah not found"
     *     )
     * )
     */
    public function ayah(string $slug, int $surah, int $ayah): JsonResponse
    {
        $recitation = QuranRecitation::query()->where('slug', $slug)->first();

        if ($recitation === null) {
            return ApiResponse::error(
                message: __('messages.quran_recitation_not_found'),
                status: 404,
            );
        }

        $recitationAyah = QuranRecitationAyah::query()
            ->where('quran_recitation_id', $recitation->id)
            ->where('surah', $surah)
            ->where('ayah', $ayah)
            ->first();

        if ($recitationAyah === null) {
            return ApiResponse::error(
                message: __('messages.quran_recitation_ayah_not_found'),
                status: 404,
            );
        }

        return ApiResponse::success(
            message: __('messages.quran_recitation_ayah_fetched_successfully'),
            data: new QuranRecitationAyahResource($recitationAyah),
            status: 200,
        );
    }
}
