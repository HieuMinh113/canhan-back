<?php
    
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
    
class GithubController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }
          
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGithubCallback()
    {
    try {
        $githubUser = Socialite::driver('github')->user();

        $user = User::updateOrCreate(
            ['email' => $githubUser->email],
            [
                'name' => $githubUser->getName() ?? $githubUser->getNickname() ?? $githubUser->getEmail(),
                'github_id' => $githubUser->id,
                'password' => encrypt('123456dummy')
            ]
        );
        $token = $user->createToken('github-login')->plainTextToken;
        return redirect()->away("http://localhost:8080/login-git-success?token={$token}&role={$user->role}&id={$user->id}");
    } catch (Exception $e) {
        return response()->json(['message' => $e->getMessage()], 401);
    }
}
}