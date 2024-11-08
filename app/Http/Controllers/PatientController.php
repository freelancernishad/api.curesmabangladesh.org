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

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'fullName' => 'string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->fill($request->only([
            'fullName', 'relationship', 'diagnosedForSMA', 'symptoms',
            'typeOfSMA', 'doctorName', 'fatherMobile', 'motherMobile',
            'emergencyContact', 'email', 'presentAddress', 'permanentAddress', 'agreement', 'dateOfBirth'
        ]));

        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::disk('protected')->delete($user->image);
            }
            $user->image = $this->storeImage($request->file('image'));
        }

        $user->save();

        return response()->json(['message' => 'Patient updated successfully', 'user' => $user], 200);
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
