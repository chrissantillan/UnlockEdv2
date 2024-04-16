<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Requests\UpdateEnrollmentRequest;
use App\Http\Resources\EnrollmentCourseJoinResource;
use App\Http\Resources\EnrollmentResource;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EnrollmentController extends Controller
{
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $perPage = request()->query('per_page', 10);
        $sortBy = request()->query('sort', 'user_id');
        $sortOrder = request()->query('order', 'asc');
        $search = request()->query('search', '');

        $query = Enrollment::query()->with(['user', 'course.providerPlatform']);
        if ($search) {
            $query->whereHas('course', function ($query) use ($search) {
                $query->where('external_course_name', 'like', '%'.$search.'%')->orWhere('external_course_code', 'like', '%', $search.'%');
            });
            $query->orWhereHas('user', function ($query) use ($search) {
                $query->where('username', 'like', '%'.$search.'%')->orWhere('user_id', 'like', '%'.$search.'%');
            });
        }
        if ($request->user()->isAdmin()) {
            $query->orderBy($sortBy, $sortOrder);
            $categories = $query->paginate($perPage);

            return EnrollmentCourseJoinResource::collection($categories);
        } else {
            $query->where(['user_id' => $request->user()->id]);
            $query->orderBy($sortBy, $sortOrder);
            $categories = $query->paginate($perPage);

            return EnrollmentCourseJoinResource::collection($categories);
        }
    }

    public function show(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        if ($request->user()->isAdmin()) {
            $enrollment = Enrollment::findOrFail($id);
            if (! $enrollment) {
                return response()->json(['error' => "Enrollment with this ID not found for User: {$request->user()->username}"], Response::HTTP_NOT_FOUND);
            }

            return new EnrollmentCourseJoinResource($enrollment);
        } else {
            $enrollment = Enrollment::findOrFail($id);
            if (! $enrollment || $enrollment->user_id != $request->user()->id) {
                return response()->json(['error' => "Enrollment with this ID not found for User: {$request->user()->username}"], Response::HTTP_NOT_FOUND);
            }

            return new EnrollmentCourseJoinResource($enrollment);
        }
    }

    public function store(StoreEnrollmentRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();

        $enrollment = Enrollment::create($validated);

        return new EnrollmentResource($enrollment);
    }

    public function update(UpdateEnrollmentRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();

        $Enrollment = Enrollment::findOrFail($id);

        if (! $Enrollment) {
            return response()->json(['error' => 'Enrollment not found'], Response::HTTP_NOT_FOUND);
        }

        $Enrollment->update($validated);

        return new EnrollmentResource($Enrollment);
    }

    public function destroy(AdminRequest $req, string $id)
    {
        $req->authorize();
        $Enrollment = Enrollment::find($id);

        if (! $Enrollment) {
            return response()->json(['error' => 'Enrollment not found'], Response::HTTP_NOT_FOUND);
        }

        $Enrollment->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
