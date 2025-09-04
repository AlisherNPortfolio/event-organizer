<?php

namespace App\Presentation\Controllers\Profile;

use App\Application\Profile\CommandHandlers\UpdateAvatarCommandHandler;
use App\Application\Profile\CommandHandlers\UpdateProfileCommandHandler;
use App\Application\Profile\Commands\UpdateAvatarCommand;
use App\Application\Profile\Commands\UpdateProfileCommand;
use App\Application\Profile\Query\GetProfileQuery;
use App\Application\Profile\Query\GetUserStatisticsQuery;
use App\Application\Profile\QueryHandlers\GetProfileQueryHandler;
use App\Application\Profile\QueryHandlers\GetUserStatisticsQueryHandler;
use App\Domain\Auth\ValueObjects\UserId;
use App\Presentation\Controllers\Controller;
use App\Presentation\Requests\Profile\UpdateAvatarRequest;
use App\Presentation\Requests\Profile\UpdateProfileRequest;
use App\Presentation\ViewModels\ProfileViewModel;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct(
        private readonly GetProfileQueryHandler $getProfileQueryHandler,
        private readonly GetUserStatisticsQueryHandler $getUserStatisticsQueryHandler,
        private readonly UpdateProfileCommandHandler $updateProfileCommandHandler,
        private readonly UpdateAvatarCommandHandler $updateAvatarCommandHandler
    )
    {}

    public function show(): View
    {
        try {
            $user = Auth::user();
            $profileQuery = new GetProfileQuery(
                new UserId($user->id)
            );
            $statisticsQuery = new GetUserStatisticsQuery(
                new UserId($user->id),
                10
            );
            $profile = $this->getProfileQueryHandler->handle($profileQuery);
            $statistics = $this->getUserStatisticsQueryHandler->handle($statisticsQuery);

            $viewModel = new ProfileViewModel($profile, $statistics);

            return view('profile.show', compact('viewModel', 'user'));
        } catch(Exception $e) {
            return view('profile.show');
        }
    }

    public function edit(): View
    {
        return view('profile.edit');
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        try {
            $command = new UpdateProfileCommand(
                new UserId(Auth::id()),
                $request->validated('name'),
                $request->validated('email'),
                $request->validated('current_password'),
                $request->validated('password'),
                $request->validated('password_confirmation')
            );
            $this->updateProfileCommandHandler->handle($command);

            return redirect()->route('profile.show')
            ->with('success', 'Profil yangilandi');
        } catch (Exception $e) {
            $message = get_exception_message("Profil ma'lumotlarini yangilashda xatolik. ", $e->getMessage());
            return back()->withErrors(['error' => $message])->withInput();
        }
    }

    public function uploadAvatar(UpdateAvatarRequest $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            $oldAvatar = $user->avatar;

            $newAvatarPath = $request->file('avatar')->store('avatars', 'public');

            if ($newAvatarPath) {
                if ($oldAvatar) {
                    Storage::disk('public')->delete($oldAvatar);
                }

                $command = new UpdateAvatarCommand(
                    new UserId($user->id),
                    $newAvatarPath
                );
                $this->updateAvatarCommandHandler->handle($command);
                $user->refresh();

                return back()
                ->with('success', 'Profil rasmi yangilandi');
            }

            return back()->withErrors(['error' => "Rasmni serverda saqlashda xatolik"]);

        } catch (Exception $e) {
            $message = get_exception_message("Profil rasmini yangilashda xatolik", $e->getMessage());
            return back()->withErrors(['error' => $message]);
        }
    }
}
