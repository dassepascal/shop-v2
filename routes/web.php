<?php

use Livewire\Volt\Volt;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IsAdminOrRedac;


Volt::route('/blog', 'blog.index')->name('blog.index');

Volt::route('/category/{slug}', 'blog.index');
Volt::route('/posts/{slug}', 'blog.index');
Volt::route('/blog/posts', 'posts.index')->name('posts.index');
Volt::route('/blog/posts/{slug}', 'posts.show')->name('posts.show');
Volt::route('/blog/search/{param}', 'index')->name('posts.search');
Volt::route('/blog/pages/{page:slug}', 'pages.show')->name('pages.show');

Volt::route('/', 'index')->name('home');
Volt::route('/pages/{page:slug}', 'page')->name('pages');
Volt::route('/products/{product}', 'product')->name('products.show');
Volt::route('/cart', 'cart')->name('cart');

Volt::route('/contact', 'contact')->name('contact');


Route::middleware('guest')->group(function () {
    Volt::route('/register', 'auth.register');
    Volt::route('/login', 'auth.login')->name('login');
    Volt::route('/forgot-password', 'auth.forgot-password');
    Volt::route('/reset-password/{token}', 'auth.reset-password')->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Volt::route('/profile', 'auth.profile')->name('profileAuth');

    Route::prefix('order')->group(function () {
        Volt::route('/creation', 'order.index')->name('order.index');
        Volt::route('/confirmation/{id}', 'order.confirmation')->name('order.confirmation');
        Volt::route('/card/{id}', 'order.card')->name('order.card');
    });

    Route::prefix('account')->group(function () {
        Volt::route('/profile', 'account.profile')->name('profile');
        Volt::route('/addresses', 'account.addresses.index')->name('addresses');
        Volt::route('/addresses/create', 'account.addresses.create')->name('addresses.create');
        Volt::route('/addresses/{address}/edit', 'account.addresses.edit')->name('addresses.edit');
        Volt::route('/orders', 'account.orders.index')->name('orders');
        Volt::route('/orders/{order}', 'account.orders.show')->name('orders.show');
        Volt::route('/rgpd', 'account.rgpd.index')->name('rgpd');
    });

    Route::middleware(IsAdminOrRedac::class)->prefix('admin')->group(function () {

        Volt::route('/posts/index', 'admin.posts.index')->name('posts.index');
        Volt::route('/posts/create', 'admin.posts.create')->name('posts.create');
        Volt::route('/posts/{post:slug}/edit', 'admin.posts.edit')->name('posts.edit');
    });
    Route::middleware(IsAdmin::class)->prefix('admin')->group(function () {
        // Dashboard principal pour choisir entre Shop et Blog
        Volt::route('/dashboard', 'admin.dashboard')->name('admin.dashboard');

        // Routes pour le Shop Admin
        Route::prefix('shop')->group(function () {
            Volt::route('/dashboard', 'admin.shop.dashboard')->name('admin.shop.dashboard');
            Volt::route('/orders', 'admin.shop.orders.index')->name('admin.shop.orders.index');
            Volt::route('/orders/{order}', 'admin.shop.orders.show')->name('admin.shop.orders.show');
            Volt::route('/products', 'admin.shop.products.index')->name('admin.shop.products.index');
            Volt::route('/products/create', 'admin.shop.products.create')->name('admin.shop.products.create');
            Volt::route('/products/{product}/edit', 'admin.shop.products.edit')->name('admin.shop.products.edit');
            Volt::route('/customers', 'admin.shop.customers.index')->name('admin.shop.customers.index');
            Volt::route('/customers/{user}', 'admin.shop.customers.show')->name('admin.shop.customers.show');
            Volt::route('/addresses', 'admin.shop.customers.addresses')->name('admin.addresses');
            Volt::route('/store', 'admin.shop.parameters.store')->name('admin.shop.parameters.store');
            Volt::route('/states', 'admin.parameters.states.index')->name('admin.shop.parameters.states.index');
            Volt::route('/states/create', 'admin.shop.parameters.states.create')->name('admin.shop.parameters.states.create');
            Volt::route('/states/{state}/edit', 'admin.shop.parameters.states.edit')->name('admin.shop.parameters.states.edit');
            Volt::route('/countries', 'admin.shop.parameters.countries.index')->name('admin.shop.parameters.countries.index');
            Volt::route('/countries/{country}/edit', 'admin.shop.parameters.countries.edit')->name('admin.shop.parameters.countries.edit');
            Volt::route('/pages', 'admin.shop.parameters.pages.index')->name('admin.shop.parameters.pages.index');
            Volt::route('/pages/create', 'admin.shop.parameters.pages.create')->name('admin.shop.parameters.pages.create');
            Volt::route('/pages/{page}/edit', 'admin.shop.parameters.pages.edit')->name('admin.shop.parameters.pages.edit');
            Volt::route('/ranges', 'admin.shop.parameters.shipping.ranges')->name('admin.shop.parameters.shipping.ranges');
            Volt::route('/rates', 'admin.shop.parameters.shipping.rates')->name('admin.shop.parameters.shipping.rates');
            Volt::route('/maintenance', 'admin.shop.maintenance')->name('admin.shop.maintenance');
            Volt::route('/products/promotion', 'admin.shop.products.promotion')->name('admin.shop.products.promotion');
            Volt::route('/stats', 'admin.shop.stats')->name('admin.shop.stats');
        });

        // Routes pour le Blog Admin
        Route::prefix('blog')->group(function () {
            Volt::route('/dashboard', 'admin.blog.dashboard')->name('admin.blog.dashboard');
            Volt::route('/posts', 'admin.blog.posts.index')->name('admin.blog.posts.index');
            Volt::route('/posts/create', 'admin.blog.posts.create')->name('admin.blog.posts.create');
            Volt::route('/posts/{post:slug}/edit', 'admin.blog.posts.edit')->name('admin.blog.posts.edit');
            Volt::route('/categories', 'admin.blog.categories.index')->name('admin.blog.categories.index');
            Volt::route('/categories/create', 'admin.blog.categories.create')->name('admin.blog.categories.create');
            Volt::route('/categories/{category}/edit', 'admin.blog.categories.edit')->name('admin.blog.categories.edit');
        });
    });
});
