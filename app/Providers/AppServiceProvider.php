<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use DateTime;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        'App\Services\Interfaces\UserServiceInterface' => 'App\Services\UserService',
        'App\Services\Interfaces\UserCatalogueServiceInterface' => 'App\Services\UserCatalogueService',
        'App\Services\Interfaces\CustomerServiceInterface' => 'App\Services\CustomerService',
        'App\Services\Interfaces\CustomerCatalogueServiceInterface' => 'App\Services\CustomerCatalogueService',
        'App\Services\Interfaces\LanguageServiceInterface' => 'App\Services\LanguageService',
        'App\Services\Interfaces\GenerateServiceInterface' => 'App\Services\GenerateService',
        'App\Services\Interfaces\PostCatalogueServiceInterface' => 'App\Services\PostCatalogueService',
        'App\Services\Interfaces\PermissionServiceInterface' => 'App\Services\PermissionService',
        'App\Services\Interfaces\PostServiceInterface' => 'App\Services\PostService',


        'App\Services\Interfaces\ProductCatalogueServiceInterface' => 'App\Services\ProductCatalogueService',
        'App\Services\Interfaces\ProductServiceInterface' => 'App\Services\ProductService',
        'App\Services\Interfaces\AttributeCatalogueServiceInterface' => 'App\Services\AttributeCatalogueService',
        'App\Services\Interfaces\AttributeServiceInterface' => 'App\Services\AttributeService',
        'App\Services\Interfaces\SystemServiceInterface' => 'App\Services\SystemService',
        'App\Services\Interfaces\MenuServiceInterface' => 'App\Services\MenuService',
        'App\Services\Interfaces\MenuCatalogueServiceInterface' => 'App\Services\MenuCatalogueService',
        'App\Services\Interfaces\SlideServiceInterface' => 'App\Services\SlideService',
        'App\Services\Interfaces\WidgetServiceInterface' => 'App\Services\WidgetService',
        'App\Services\Interfaces\PromotionServiceInterface' => 'App\Services\PromotionService',
        'App\Services\Interfaces\SourceServiceInterface' => 'App\Services\SourceService',

    ];



    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach($this->bindings as $key => $val)
        {
            $this->app->bind($key, $val);
        }
        $this->app->register(RepositoryServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('custom_date_format',function($attribute, $value, $parameters, $validator){
            return Datetime::createFromFormat('d/m/Y H:i', $value) !== false;
        });

        Validator::extend('custom_after',function($attribute, $value, $parameters, $validator){
            $startDate = Carbon::createFromFormat('d/m/Y H:i', $validator->getData()[$parameters[0]]);
            $endDate = Carbon::createFromFormat('d/m/Y H:i', $value);
            return $endDate->greaterThan($startDate) !== false;//so sánh ngày kết thúc với ngày bắt đầu
        });

        Schema::defaultStringLength(191);
    }
}
