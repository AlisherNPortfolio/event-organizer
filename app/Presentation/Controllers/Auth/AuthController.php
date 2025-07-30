<?php

namespace App\Presentation\Controllers\Auth;

use App\Application\Auth\Commands\LoginUserCommand;
use App\Application\Auth\Commands\RegisterUserCommand;
use App\Application\Auth\Services\AuthService;
use App\Application\Bus\ICommandBus;
use App\Domain\Auth\ValueObjects\Password;
use App\Domain\Auth\ValueObjects\UserEmail;
use App\Presentation\Controllers\Controller;
use App\Presentation\Requests\Auth\LoginRequest;
use App\Presentation\Requests\Auth\RegisterRequest;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
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
                Auth::loginUsingId($userId);
                event(new Registered(Auth::user()));
                // vaqtincha. Productionda o'chiriladi
                Auth::user()->update(['is_active' => 1, 'email_verified_at' => now()]);
            }

            return redirect()->route('login')->with('success', 'Ro\'yxatdan o\'tish muvaffaqiyatli amalga oshirildi.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function login()
    {
        return view('auth.login');
    }

    public function loginPost(LoginRequest $request): RedirectResponse
    {
        try {
            $command = new LoginUserCommand(
                email: new UserEmail($request->validated('email')),
                password: new Password($request->validated('password')),
                remember: $request->validated('remember', false)
            );

            $userId = $this->commandBus->dispatch($command);

            if ($userId) {
                Auth::loginUsingId($userId);
                $user = Auth::user();
                $user->updateLastLogin();

                $route = 'events.index';
                $message = 'Kirish muvaffaqiyatli amalga oshirildi.';

                if ($user->isAdmin()) {
                    $route = 'dashboard';
                    $message = 'Xush kelibsiz, admin!';
                }

                return redirect()->route($route)->with('success', $message);
            } else {
                return back()->withErrors(['error' => "Email yoki parol noto'g'ri."]);
            }

        } catch (Exception $e) {
            $message = get_exception_message("Login qilishda xatolik yuz berdi: ", $e->getMessage());
            return back()->withErrors(['error' => $message]);
        }
    }

    public function logout()
    {
        try {dd('test');
            Auth::logout();
            return redirect()->route('login')->with('success', 'Tizimdan muvaffaqiyatli chiqildi.');
        } catch (Exception $e) {
            $message = get_exception_message("Tizimdan chiqishda xatolik yuz berdi: ", $e->getMessage());
            return back()->withErrors(['error' => $message]);
        }
    }
}
