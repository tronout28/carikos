<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\University;

class UniversityController extends Controller
{
    public function index()
    {
        $university = University::all();
        return response()->json([
            'status' => 'success',
            'data' => $university
        ]);
    }

    public function show($id)
    {
        $university = University::find($id);
        if ($university) {
            return response()->json([
                'status' => 'success',
                'data' => $university
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'University not found'
            ], 404);
        }
    }

    public function Insertuniversity(Request $request)
    {
        $request->validate([
            'university' => 'required|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
        ]);

        $university = new University([
            'university' => $request->university,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        $university->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully created university!',
        ], 201);
    }

    public function Updateuniversity(Request $request, $id)
    {
        $university = University::find($id);
        if ($university) {
            $request->validate([
                'university' => 'required|string',
                'latitude' => 'required|string',
                'longitude' => 'required|string',
            ]);

            $university->university = $request->university;
            $university->latitude = $request->latitude;
            $university->longitude = $request->longitude;

            $university->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully updated university!'
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'University not found'
            ], 404);
        }
    }

    public function Deleteuniversity($id)
    {
        $university = University::find($id);
        if ($university) {
            $university->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully deleted university!'
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'University not found'
            ], 404);
        }
    }

    
}
