<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

         if (app()->environment('production')) {
        URL::forceScheme('https');
    }

         Blade::component('components.task-button', 'task-button');

         VerifyEmail::toMailUsing(function (object $notifiable,string $url){
            return (new MailMessage)->subject("Verify Your Email Address at Taskly")
            ->line("click the button below to verify your email address")->action("Verify Email Address",$url)
            ->line("If you did not create an account, no further action is required.")->action('verfy email address',$url);
         });
    }
}
