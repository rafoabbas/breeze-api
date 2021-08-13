<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PasswordResetLinkController
 * @package App\Http\Controllers\Auth
 */
class PasswordResetLinkController extends Controller
{

    /**
     * @param Request $request
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $status = Password::sendResetLink(['email' => $input['email']]);

        return $status == Password::RESET_LINK_SENT
            ? responder()->success(['message' => 'Password reset link sent'])
            : responder()->error(Response::HTTP_BAD_REQUEST, 'Failed to send password reset link')->respond(Response::HTTP_BAD_REQUEST);
    }
}
