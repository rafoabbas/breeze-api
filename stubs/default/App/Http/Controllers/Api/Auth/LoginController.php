<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /**
     * @param Request $request
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder
     * @throws ValidationException
     */
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

    /**
     * @param Request $request
     * @return mixed
     */
    protected function validateLoginData(Request $request)
    {
        return $request->validate([
            'email' => ['required', 'email:filter', 'exists:users,email'],
            'password' => ['required', 'min:8'],
        ]);
    }
}
