<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use RestResponseFactory;

class CustomerController extends Controller
{
    public function login(Request $request) {
        $req = $request->all();
        $validatorObj = Validator::make($req,[
            'email' => 'required|exists:customers,email',
            'password' => 'required',
        ]);

        $errorsArr = array();

        if ($validatorObj->fails()) {
            $errorsArr = array_merge($errorsArr, $validatorObj->messages()->all(':message'));
        }

        if (count($errorsArr) > 0) {
            return RestResponseFactory::badrequest([], implode("", $errorsArr))->toJSON();
        }

        if (Auth::guard('customer')->attempt(['email' => $req['email'], 'password' => $req['password']])) {
            $customer = Auth::guard('customer')->user();
            $token = $customer->createToken('loan-app', ['customer'])->plainTextToken;
            $customer['token'] = $token;
            return RestResponseFactory::success($customer, 'Login successful')->toJSON();
        }
    }

    public function profile() {
        $customer = Auth::user();
        if(!$customer) {
            return RestResponseFactory::not_found([], 'Customer not found.')->toJSON();
        }
        return RestResponseFactory::success($customer, 'Customer Profile.')->toJSON();
    }
}
