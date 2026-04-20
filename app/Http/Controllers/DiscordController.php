<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Facades\Alert;
use App\Models\DiscordAccount;
use App\Traits\DiscordTrait;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class DiscordController extends Controller
{
    use DiscordTrait;

    // public function callback()
    // {
    //     $type = session('discord_type');
    //     if (empty($type)) {
    //         Alert::addAlert(__('general.error'), 'danger');

    //         return redirect()->back();
    //     }
    //     try {
    //         $discord = Socialite::driver('discord')->user();
    //         $guild_id = env('PRIMARY_GUILD_ID');
    //         $discord_id = $discord->id;
    //         $avatar = $discord->avatar;
    //         $token = $discord->token;

    //         if ($type == 'AUTH') {
    //             $discord_user = DiscordAccount::findByDiscordId($discord_id);
    //             if (empty($discord_user)) {
    //                 Alert::addAlert('Discord-Account nicht verknüpft!', 'danger');

    //                 return redirect()->back();
    //             }
    //         }
    //         try {
    //             $response = $this->makeDiscordRequest(
    //                 "/users/@me/guilds/{$guild_id}/member",
    //                 [
    //                     'headers' => [
    //                         'Authorization' => 'Bearer ' . $token,
    //                     ],
    //                 ],
    //                 'GET'
    //             );
    //             $guild = json_decode($response->getBody()->getContents(), true);
    //         } catch (\GuzzleHttp\Exception\RequestException $e) {
    //         }
    //         $username = empty($guild['nick']) ? $discord->name : $guild['nick'];

    //         if (empty($discord_user)) {
    //             $discord_user = DiscordAccount::firstOrNew(
    //                 [
    //                     'discord_id' => $discord_id,
    //                 ]
    //             );
    //         }
    //         $discord_user->setDiscordId($discord_id);
    //         $discord_user->setUsername($username);
    //         $discord_user->setAvatar($avatar ?? '');
    //         $discord_user->setToken($token);

    //         switch ($type) {
    //             case 'LINK':
    //                 Alert::addAlert('Discord-Account erfolgreich verknüpft!', 'success');
    //                 $discord_user->setUserId(auth()->user()->getId());
    //                 $discord_user->save();

    //                 return redirect()->route('user.show', auth()->user());
    //             case 'AUTH':
    //                 if ($discord_user->isDirty()) {
    //                     $discord_user->save();
    //                 }
    //                 Auth::loginUsingId($discord_user->user->getId());
    //                 if ($discord_user->user->hasRole(Role::AUSBILDER->value)) {
    //                     return redirect()->route('ausbilder.index');
    //                 }

    //                 return redirect()->route('home');
    //         }
    //     } catch (\Exception $e) {
    //         Alert::addAlert('Fehler beim Abrufen der Discord-Daten', 'danger');
    //         \Log::error('Discord callback error: ' . $e->getMessage());
    //     }

    //     switch ($type) {
    //         case 'LINK':
    //             return redirect()->route('user.show', auth()->user());
    //         case 'AUTH':
    //             return redirect()->route('login.index');
    //         default:
    //             return redirect()->route('home');
    //     }
    // }
}
