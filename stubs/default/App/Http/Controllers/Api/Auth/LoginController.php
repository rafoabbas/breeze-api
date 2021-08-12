<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{

    public function login(Request $request)
    {
        $this->validateLoginData($request);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return responder()->success($user, UserTransformer::class);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->where('token', $request->api_token)->delete();

        return responder()->success(['message' => 'You have successfully logout!'])->respond(Response::HTTP_NO_CONTENT);
    }

    protected function validateLoginData(Request $request)
    {
        return $request->validate([
            'email' => ['required', 'email:filter', 'exists:users,email'],
            'password' => ['required', 'min:8'],
        ]);
    }
}
