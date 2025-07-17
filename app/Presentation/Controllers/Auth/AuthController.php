<?php

namespace App\Presentation\Controllers\Auth;

use App\Application\Auth\Commands\RegisterUserCommand;
use App\Application\Auth\Services\AuthService;
use App\Application\Bus\ICommandBus;
use App\Domain\Auth\ValueObjects\Password;
use App\Domain\Auth\ValueObjects\UserEmail;
use App\Presentation\Controllers\Controller;
use App\Presentation\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        protected ICommandBus $commandBus,
        protected AuthService $service
    )
    {}

    public function register()
    {
        return view('auth.register');
    }

    public function registerPost(RegisterRequest $request)
    {
        try {
            $command = new RegisterUserCommand(
                    name: $request->validated('name'),
                    email: new UserEmail($request->validated('email')),
                    password: new Password($request->validated('password')),
                    passwordConfirmation: $request->validated('password_confirmation')
            );

            $userId = $this->commandBus->dispatch($command);

            $user = $this->service->getUserByEmail($command->email);
            if ($user) {
                // dispatch event
                Auth::loginUsingId($userId);
                return redirect()->route('login')->with('success', 'Ro\'yxatdan o\'tish muvaffaqiyatli amalga oshirildi.');
            }

            return redirect()->route('login')->with('success', 'Ro\'yxatdan o\'tish muvaffaqiyatli amalga oshirildi.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
