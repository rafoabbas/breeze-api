<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VerifyEmailController
 * @package App\Http\Controllers\Api\Auth
 */
class VerifyEmailController extends Controller
{
    /**
     * @param $id
     * @param $hash
     * @return \Flugg\Responder\Http\Responses\SuccessResponseBuilder|string
     */
    public function __invoke($id, $hash)
    {
        $user = User::findOrFail($id);

        abort_unless($this->isHashValid($user, $hash), Response::HTTP_BAD_REQUEST, 'Invalid verification data');

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('auth.action.success')->with('status', __('Your email has already been verified.'));
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('auth.action.success')->with('status', __('Your email has been successfully verified!'));
    }

    protected function isHashValid($user, $hash)
    {
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return false;
        }

        return true;
    }
}
