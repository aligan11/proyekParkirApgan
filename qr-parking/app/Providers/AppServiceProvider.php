<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

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
        // Konfigurasi Sanctum
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        // Konfigurasi Scramble (API docs)
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });

        // View composer global: kirim data PropertyAplikasi ke semua view
        view()->composer('*', function ($view) {
            $property = \App\Models\PropertyAplikasi::first();

            if ($property) {
                $view->with([
                    'nama_aplikasi'     => $property->nama_aplikasi,
                    'kode_parkir'       => $property->kode_parkir,
                    'owner_parkir'      => $property->owner_parkir,
                    'email_perusahaan'  => $property->email_perusahaan,
                    'alamat_perusahaan' => $property->alamat_perusahaan,
                    'no_perusahaan'     => $property->no_perusahaan,
                    'logo_aplikasi'     => $property->logo_aplikasi,
                    'logo'              => $property,
                ]);
            }
        });
    }
}
