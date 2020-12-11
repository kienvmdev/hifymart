<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Observers\BrandObserver;
use App\Observers\ProductObserver;
use App\Observers\CategoryObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap the application services.
   */
  public function boot(): void
  {
    Brand::observe(BrandObserver::class);
    Product::observe(ProductObserver::class);
    Category::observe(CategoryObserver::class);
  }
}