<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Doner;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string|max:255',
            'relationship' => 'nullable|string|max:255',
            'diagnosedForSMA' => 'required|boolean',
            'symptoms' => 'required|boolean',
            'typeOfSMA' => 'nullable|string|max:255',
            'doctorName' => 'nullable|string|max:255',
            'fatherMobile' => 'nullable|string|max:255',
            'motherMobile' => 'nullable|string|max:255',
            'emergencyContact' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'presentAddress' => 'nullable|string|max:255',
            'permanentAddress' => 'nullable|string|max:255',
            'agreement' => 'required|boolean',
            'dateOfBirth' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Image validation
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userData = [
            'name' => $request->fullName,
            'fullName' => $request->fullName,
            'relationship' => $request->relationship,
            'diagnosedForSMA' => $request->diagnosedForSMA,
            'symptoms' => $request->symptoms,
            'typeOfSMA' => $request->typeOfSMA,
            'doctorName' => $request->doctorName,
            'fatherMobile' => $request->fatherMobile,
            'motherMobile' => $request->motherMobile,
            'emergencyContact' => $request->emergencyContact,
            'email' => $request->email,
            'mobile' => '01909756552',
            'presentAddress' => $request->presentAddress,
            'permanentAddress' => $request->permanentAddress,
            'agreement' => $request->agreement,
            'dateOfBirth' => $request->dateOfBirth,
            'password' => Hash::make('defaultpassword'),
        ];

        if ($request->hasFile('image')) {
            $userData['image'] = $this->storeImage($request->file('image'));
        }

        $user = User::create($userData);

        return response()->json(['message' => 'Patient registered successfully', 'user' => $user], 201);
    }

    private function storeImage($file)
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        return $file->storeAs('post/banner', $fileName, 'protected');
    }

    public function updateDescriptions(Request $request, $id)
    {
        // return $request->all();
        // Validate the input
        $validator = Validator::make($request->all(), [
            'short_description' => 'nullable|string|max:255',
            'long_description' => 'nullable|string',
            'annual_cost' => 'nullable',
            'total_cost' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the user by ID
        $user = User::findOrFail($id);

        // Check if a new image file is uploaded
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($user->image) {
                Storage::disk('protected')->delete($user->image);
            }

            // Store the new image
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('user_images', $fileName, 'protected');

            // Update the user's image path
            $user->image = $filePath;
        }

        // Update the descriptions and other fields
        $user->short_description = $request->input('short_description', $user->short_description);
        $user->long_description = $request->input('long_description', $user->long_description);
        $user->annual_cost = $request->input('annual_cost', $user->annual_cost);
        $user->total_cost = $request->input('total_cost', $user->total_cost);
        $user->save();

        return response()->json(['message' => 'Descriptions and image updated successfully', 'user' => $user], 200);
    }

    public function deleteImage($id)
    {
        $user = User::findOrFail($id);

        if ($user->image) {
            Storage::disk('protected')->delete($user->image);
            $user->image = null;
            $user->save();
        }

        return response()->json(['message' => 'Image deleted successfully'], 200);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user, 200);
    }

    public function getUsers(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $users = User::paginate($perPage);

        return response()->json($users, 200);
    }

    // Additional methods as previously defined (donate, updateDescriptions, etc.) ...
}
