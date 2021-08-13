<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

/**
 * Class EmailVerificationNotificationController
 * @package App\Http\Controllers\Api\Auth
 */
class EmailVerificationNotificationController extends Controller
{
    /**
     * @param Request $request
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if ($user->hasVerifiedEmail()) {
            return responder()->success(['message' => __('Your email has already been verified'),]);
        }

        $user->sendEmailVerificationNotification();

        return responder()->success(['message' => __('Email verification link sent')]);
    }
}
