<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kost;
use App\Models\UniversityKost;
use App\Models\University;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class KostController extends Controller
{

    public function insertkost(Request $request)
    {
        $user = Auth::user();  

        // Debug: Periksa user
        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized',
                'debug' => 'No authenticated user found'
            ], 401);
        }
    
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|string',
            'phone_number' => 'nullable|string',
            'kost_type' => ['required',Rule::in(['kost_reguler','kost_exclusive','kontrakan'])],
            'image' =>  'required|image|mimes:jpeg,png,jpg,gif,svg|max:5000',
            'description' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'regency' => 'required|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
        ]);

        $kost = Kost::create([
            'user_id' => $user->id, 
            'owner' => $user->name,  
            'name' => $request->name,
            'kost_type' => $request->kost_type,
            'price' => $request->price,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
            'address' => $request->address,
            'city' => $request->city,
            'regency' => $request->regency,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        

        // Menangani upload gambar
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('images-kost'), $imageName);

            // Jika ada gambar sebelumnya, hapus gambar lama
            if ($kost->image && file_exists(public_path('images-kost/' . $kost->image))) {
                unlink(public_path('images-kost/' . $kost->image));
            }

            // Menyimpan nama gambar ke database
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
                'kost_type' => ['nullable',Rule::in(['kost_reguler','kost_exclusive','kontrakan'])],
                'phone_number' => 'nullable|string',
                'image' =>  'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5000',
                'description' => 'nullable|string',
                'address' => 'nullable|string',
                'city' => 'nullable|string',
                'regency' => 'nullable|string',
            ]);

            $kost->name = $request->name;
            $kost->kost_type = $request->kost_type;
            $kost->price = $request->price;
            $kost->phone_number = $request->phone_number;
            $kost->description = $request->description;
            $kost->address = $request->address;
            $kost->city = $request->city;
            $kost->regency = $request->regency;


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

    public function deletekost($id)
    {
        $kost = Kost::find($id);
        if ($kost) {
            if ($kost->image && file_exists(public_path('images-kost/' . $kost->image))) {
                unlink(public_path('images-kost/' . $kost->image));
            }

            $kost->delete();

            return response()->json([
                'message' => 'Successfully deleted kost!',
            ]);
        } else {
            return response()->json([
                'message' => 'Kost not found',
            ], 404);
        }
    }

    public function index()
    {
        $kosts = Kost::with(['universities' => function ($query) {
            $query->select('universities.id', 'universities.university');
        }])->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully retrieved kosts with universities',
            'data' => $kosts
        ]);
    }

    public function show($id)
    {
        $kost = Kost::with(['universities' => function ($query) {
            $query->select('universities.id', 'universities.university');
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
