<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LessonDetailResource;
use Illuminate\Http\Request;

class CourseController extends Controller
{

    public function index()
    {
        // Fetch all courses from the database
        $courses = Course::all();

        // Use the Resource to format them into your exact TypeScript interface
        return \App\Http\Resources\CourseResource::collection($courses);
    }

    public function show(Request $request, $slug)
    {
        $course = Course::with(['chapters.lessons' => function ($query) {
            $query->orderBy('order'); // Ensure lessons are in order
        }])->where('slug', $slug)->firstOrFail();

        return new CourseResource($course);
    }

    public function getLesson(Request $request, $slug)
    {
        $lesson = Lesson::where('slug', $slug)->firstOrFail();

        return new LessonDetailResource($lesson);
    }
}
