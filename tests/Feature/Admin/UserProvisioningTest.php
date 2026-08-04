<?php

use App\Mail\OneTimePasswordMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(fn () => seedRbac());

test('creating a user emails a one-time password and requires password change', function () {
    Mail::fake();

    $superAdmin = User::factory()->superAdmin()->create();
    $editorId = Role::query()->where('name', 'editor')->value('id');

    $this->actingAs($superAdmin)->post(route('admin.users.store'), [
        'first_name' => 'New',
        'last_name' => 'Editor',
        'email' => 'new-editor@example.com',
        'phone' => '9876543210',
        'role' => $editorId,
    ])->assertRedirect(route('admin.users.index'));

    $user = User::query()->where('email', 'new-editor@example.com')->firstOrFail();

    expect($user->must_change_password)->toBeTrue()
        ->and($user->is_active)->toBeTrue()
        ->and($user->phone)->toBe('9876543210');

    Mail::assertSent(OneTimePasswordMail::class, function (OneTimePasswordMail $mail) use ($user): bool {
        return $mail->hasTo($user->email)
            && $mail->reason === 'account'
            && $mail->oneTimePassword !== '';
    });
});

test('changing a user email emails a new one-time password', function () {
    Mail::fake();

    $superAdmin = User::factory()->superAdmin()->create();
    $user = User::factory()->editor()->create([
        'email' => 'old@example.com',
        'must_change_password' => false,
    ]);
    $editorId = Role::query()->where('name', 'editor')->value('id');

    $this->actingAs($superAdmin)->put(route('admin.users.update', $user), [
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'email' => 'new@example.com',
        'phone' => $user->phone,
        'role' => $editorId,
        'is_active' => '1',
    ])->assertRedirect(route('admin.users.index'));

    expect($user->fresh())
        ->email->toBe('new@example.com')
        ->must_change_password->toBeTrue()
        ->email_verified_at->toBeNull();

    Mail::assertSent(OneTimePasswordMail::class, function (OneTimePasswordMail $mail): bool {
        return $mail->hasTo('new@example.com') && $mail->reason === 'email_changed';
    });
});

test('super admin can deactivate a user', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $user = User::factory()->editor()->create(['is_active' => true]);
    $editorId = Role::query()->where('name', 'editor')->value('id');

    $this->actingAs($superAdmin)->put(route('admin.users.update', $user), [
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'email' => $user->email,
        'role' => $editorId,
        'is_active' => '0',
    ])->assertRedirect(route('admin.users.index'));

    expect($user->fresh()->is_active)->toBeFalse();
});

test('users index shows phone and status', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    User::factory()->editor()->create([
        'phone' => '9876543210',
        'is_active' => false,
    ]);

    $this->actingAs($superAdmin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertSee('9876543210', false)
        ->assertSee('Inactive', false)
        ->assertSee('Phone', false);
});
