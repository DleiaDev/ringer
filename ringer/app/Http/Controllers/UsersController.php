<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Lib\Image;
use Hash;

class UsersController extends Controller
{
    // Update user
    public function updateUser(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
          'name' => 'required|string',
          'email' => 'required|string|unique:users,email,'.$user->id
        ]);

        $user->update($request->all());

        return $request->all();
    }

    // Update user photo
    public function updatePhoto(Request $request)
    {
        $this->validate($request, [
          'photo' => 'required|mimes:png,jpeg,jpg,svg|max:2048|dimensions:min_width=400,min_height=400'
        ]);

        $user = auth()->user();

        $imageLink = Image::upload($request->file('photo'));

        if ($user->photo != Image::$default)
          Image::delete($user->photo);

        $user->update(['photo' => $imageLink]);

        return response()->json(['photo' => $imageLink]);
    }

    // Change password
    public function changePassword(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
          'currentPassword' => 'required',
          'newPassword' => 'required|string|confirmed|min:6',
          'newPassword_confirmation' => 'required'
        ]);

        $errors = (object)[];

        if (!Hash::check($request->currentPassword, $user->password)) {
          $errors->currentPassword = ['Incorrect current password.'];
          return response()->json(['message' => 'The given data was invalid.', 'errors' => $errors], 422);
        }

        if (strcmp($request->currentPassword, $request->newPassword) == 0) {
          $errors->newPassword = ['New password and current password cannot be the same.'];
          return response()->json(['message' => 'The given data was invalid.', 'errors' => $errors], 422);
        }

        $user->password = bcrypt($request->newPassword);

        $user->save();
    }
}
