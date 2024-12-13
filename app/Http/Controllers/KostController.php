<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kost;
use App\Models\UniversityKost;
use App\Models\University;


class KostController extends Controller
{

    public function insertkost(Request $request)
    {
      $request->validate([
            'user_id' => 'required|integer',
            'name' => 'required|string',
            'price' => 'required|string',
            'phone_number' => 'nullable|string',
            'image' =>  'required|image|mimes:jpeg,png,jpg,gif,svg|max:5000',
            'description' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'regency' => 'required|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'university' => 'required|string',
      ]); 

        $kost = Kost::create([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'price' => $request->price,
                'phone_number' => $request->phone_number,
                'description' => $request->description,
                'address' => $request->address,
                'city' => $request->city,
                'regency' => $request->regency,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('images-kost'), $imageName);

            if ($kost->image && file_exists(public_path('images-kost/' . $kost->image))) {
                unlink(public_path('images-kost/' . $kost->image));
            }

            $kost->image = $imageName;
            $kost->image = url('images-kost/' . $kost->image);

            $kost->save();
        }

        return response()->json([
            'message' => 'Successfully created kost!',
            'kost' => $kost,
        ], 201);
    }

    public function attachUniversity(Request $request)
    {
        $request->validate([
            'university_id' => 'required|integer',
            'kost_id' => 'required|integer',
        ]);

        $university = University::find($request->university_id);
        if (!$university) {
            return response()->json([
                'status' => 'failed',
                'message' => 'University not found'
            ], 404);
        }

        $kost = Kost::find($request->kost_id);
        if (!$kost) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Kost not found'
            ], 404);
        }

        $universityKost = new UniversityKost([
            'university_id' => $request->university_id,
            'kost_id' => $request->kost_id,
        ]);

        $universityKost->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully attached university to kost!',
        ], 201);
    }

    public function updatekost(Request $request, $id)
    {
        $kost = Kost::find($id);
        if ($kost) {
            $request->validate([
                'name' => 'nullable|string',
                'price' => 'nullable|string',
                'phone_number' => 'nullable|string',
                'image' =>  'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5000',
                'description' => 'nullable|string',
                'address' => 'nullable|string',
                'city' => 'nullable|string',
                'regency' => 'nullable|string',
                'latitude' => 'nullable|string',
                'longitude' => 'nullable|string',
            ]);

            $kost->name = $request->name;
            $kost->price = $request->price;
            $kost->phone_number = $request->phone_number;
            $kost->description = $request->description;
            $kost->address = $request->address;
            $kost->city = $request->city;
            $kost->regency = $request->regency;
            $kost->latitude = $request->latitude;
            $kost->longitude = $request->longitude;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->extension();
                $image->move(public_path('images-kost'), $imageName);

                if ($kost->image && file_exists(public_path('images-kost/' . $kost->image))) {
                    unlink(public_path('images-kost/' . $kost->image));
                }

                $kost->image = $imageName;
                $kost->image = url('images-kost/' . $kost->image);

                $kost->save();
            }

            return response()->json([
                'message' => 'Successfully updated kost!',
                'kost' => $kost,
            ]);
        } else {
            return response()->json([
                'message' => 'Kost not found',
            ], 404);
        }
    }

    public function index()
    {
        $kosts = Kost::with(['universities' => function($query) {
            $query->select('universities.id', 'universities.name');
        }])->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully retrieved kosts with universities',
            'data' => $kosts
        ]);
    }

    public function show($id)
    {
        $kost = Kost::with(['universities' => function($query) {
            $query->select('universities.id', 'universities.name');
        }])->find($id);

        if ($kost) {
            return response()->json([
                'status' => 'success',
                'data' => $kost
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Kost not found'
            ], 404);
        }
    }

}
