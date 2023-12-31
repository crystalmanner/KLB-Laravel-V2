<?php

namespace Webkul\SocialLogin\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;

class EventServiceProvider extends ServiceProvider
{   
    protected $listen = [
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            // ... other providers
            'SocialiteProviders\\InstagramBasic\\InstagramBasicExtendSocialite@handle',
            'SocialiteProviders\\Instagram\\InstagramExtendSocialite@handle',
        ],
        
    ];
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('bagisto.shop.customers.login_form_controls.before', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('sociallogin::shop.customers.session.social-links');
            
        });
        
    }
}
