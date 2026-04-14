<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController extends Controller
{
    public function emailVerification(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully'
        ]);
    }
}
