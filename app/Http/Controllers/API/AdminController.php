<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use RestResponseFactory;

class AdminController extends Controller
{
    public function login(Request $request) {
        $req = $request->all();
        $validatorObj = Validator::make($req,[
           'email' => 'required|exists:admins,email',
           'password' => 'required',
        ]);

        $errorsArr = array();

        if ($validatorObj->fails()) {
            $errorsArr = array_merge($errorsArr, $validatorObj->messages()->all(':message'));
        }

        if (count($errorsArr) > 0) {
            return RestResponseFactory::badrequest([], implode("", $errorsArr))->toJSON();
        }

        if (Auth::guard('admin')->attempt(['email' => $req['email'], 'password' => $req['password']])) {
            $admin = Auth::guard('admin')->user();
            $token = $admin->createToken('loan-app', ['admin'])->plainTextToken;
            $admin['token'] = $token;
            return RestResponseFactory::success($admin, 'Login successful')->toJSON();
        }
    }

    public function profile() {
        $admin = Auth::user();
        if(!$admin) {
            return RestResponseFactory::not_found([], 'User not found.')->toJSON();
        }
        return RestResponseFactory::success($admin, 'Admin Profile.')->toJSON();
    }
}
