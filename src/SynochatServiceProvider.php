<?php


namespace KosmosKosmos\SynoChat;

use Illuminate\Support\ServiceProvider;

class SynochatServiceProvider extends ServiceProvider {

    public function boot() {

        $this->publishes([
             __DIR__ . '/../config/synochat.php' => config_path('synochat.php'),
        ], 'config');

    }

}
