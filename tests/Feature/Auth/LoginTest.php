<?php

use App\Models\{Shop, User};
use Illuminate\Support\Facades\{Auth, RateLimiter, View};
use Livewire\Volt\Volt;

beforeEach(function () {
    // Partagé par AppServiceProvider hors console uniquement
    View::share('shop', Shop::factory()->create());
    RateLimiter::clear('jean@example.com|127.0.0.1');
    $this->user = User::factory()->create(['email' => 'jean@example.com']);
});

it('displays the login page to guests', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSeeLivewire('auth.login');
});

it('redirects authenticated users away from the login page', function () {
    $this->actingAs($this->user)
        ->get('/login')
        ->assertRedirect();
});

it('logs in with valid credentials', function () {
    Volt::test('auth.login')
        ->set('email', 'jean@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(url('/'));

    $this->assertAuthenticatedAs($this->user);
});

it('redirects to the intended page after login', function () {
    session()->put('url.intended', url('/profile'));

    Volt::test('auth.login')
        ->set('email', 'jean@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(url('/profile'));
});

it('remembers the user when asked', function () {
    Volt::test('auth.login')
        ->set('email', 'jean@example.com')
        ->set('password', 'password')
        ->set('remember', true)
        ->call('login');

    expect(Auth::guard('web')->getCookieJar()->getQueuedCookies())
        ->toHaveCount(1)
        ->and(Auth::guard('web')->getCookieJar()->getQueuedCookies()[0]->getName())
        ->toBe(Auth::guard('web')->getRecallerName());
});

it('rejects a wrong password', function () {
    Volt::test('auth.login')
        ->set('email', 'jean@example.com')
        ->set('password', 'mauvais')
        ->call('login')
        ->assertHasErrors('email')
        ->assertNoRedirect();

    $this->assertGuest();
});

it('rejects an unknown email', function () {
    Volt::test('auth.login')
        ->set('email', 'inconnu@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors('email');

    $this->assertGuest();
});

it('validates the fields', function (string $email, string $password, array $errors) {
    Volt::test('auth.login')
        ->set('email', $email)
        ->set('password', $password)
        ->call('login')
        ->assertHasErrors($errors);

    $this->assertGuest();
})->with([
    'empty fields' => ['', '', ['email' => 'required', 'password' => 'required']],
    'invalid email' => ['pas-un-email', 'password', ['email' => 'email']],
]);

it('locks the account after five failed attempts', function () {
    $component = Volt::test('auth.login')->set('email', 'jean@example.com')->set('password', 'mauvais');
    foreach (range(1, 5) as $attempt) {
        $component->call('login');
    }

    // Même le bon mot de passe est refusé pendant le blocage
    $component->set('password', 'password')
        ->call('login')
        ->assertHasErrors('email');

    expect($component->errors()->first('email'))
        ->toStartWith(str(trans('auth.throttle'))->before(':seconds')->toString());

    $this->assertGuest();
});
