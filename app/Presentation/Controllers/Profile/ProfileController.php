<?php

namespace App\Presentation\Controllers\Profile;

use App\Application\Profile\Query\GetProfileQuery;
use App\Application\Profile\Query\GetUserStatisticsQuery;
use App\Application\Profile\QueryHandlers\GetProfileQueryHandler;
use App\Application\Profile\QueryHandlers\GetUserStatisticsQueryHandler;
use App\Domain\Auth\ValueObjects\UserId;
use App\Presentation\Controllers\Controller;
use App\Presentation\ViewModels\ProfileViewModel;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(
        private readonly GetProfileQueryHandler $getProfileQueryHandler,
        private readonly GetUserStatisticsQueryHandler $getUserStatisticsQueryHandler
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
}
