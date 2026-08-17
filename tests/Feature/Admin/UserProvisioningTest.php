<?php

use App\Mail\AccountStatusMail;
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

test('super admin can deactivate and reactivate a user, and each change is emailed', function () {
    Mail::fake();

    $superAdmin = User::factory()->superAdmin()->create();
    $user = User::factory()->editor()->create(['is_active' => true]);

    $this->actingAs($superAdmin)
        ->put(route('admin.users.status.update', $user))
        ->assertRedirect(route('admin.users.index'));

    expect($user->fresh()->is_active)->toBeFalse();

    Mail::assertSent(AccountStatusMail::class, fn (AccountStatusMail $mail): bool => $mail->hasTo($user->email) && $mail->activated === false);

    $this->actingAs($superAdmin)
        ->put(route('admin.users.status.update', $user))
        ->assertRedirect(route('admin.users.index'));

    expect($user->fresh()->is_active)->toBeTrue();

    Mail::assertSent(AccountStatusMail::class, fn (AccountStatusMail $mail): bool => $mail->hasTo($user->email) && $mail->activated === true);
});

test('a pending user cannot be deactivated', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $user = User::factory()->editor()->create([
        'is_active' => true,
        'must_change_password' => true,
    ]);

    $this->actingAs($superAdmin)
        ->put(route('admin.users.status.update', $user))
        ->assertSessionHasErrors('user');

    expect($user->fresh()->is_active)->toBeTrue();
});

test('super admin can resend the one-time password to a pending user', function () {
    Mail::fake();

    $superAdmin = User::factory()->superAdmin()->create();
    $user = User::factory()->editor()->create([
        'is_active' => true,
        'must_change_password' => true,
    ]);

    $this->actingAs($superAdmin)
        ->post(route('admin.users.otp.resend', $user))
        ->assertRedirect(route('admin.users.index'));

    Mail::assertSent(OneTimePasswordMail::class, fn (OneTimePasswordMail $mail): bool => $mail->hasTo($user->email) && $mail->reason === 'resend');
});

test('the one-time password cannot be resent to a non pending user', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $user = User::factory()->editor()->create([
        'is_active' => true,
        'must_change_password' => false,
    ]);

    $this->actingAs($superAdmin)
        ->post(route('admin.users.otp.resend', $user))
        ->assertSessionHasErrors('user');
});

test('an inactive user\'s email address cannot be changed', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $user = User::factory()->editor()->create([
        'email' => 'old@example.com',
        'is_active' => false,
    ]);
    $editorId = Role::query()->where('name', 'editor')->value('id');

    $this->actingAs($superAdmin)->put(route('admin.users.update', $user), [
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'email' => 'new@example.com',
        'phone' => $user->phone,
        'role' => $editorId,
    ])->assertSessionHasErrors('email');

    expect($user->fresh()->email)->toBe('old@example.com');
});

test('super admin cannot deactivate their own account', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->put(route('admin.users.status.update', $superAdmin))
        ->assertSessionHasErrors('user');

    expect($superAdmin->fresh()->is_active)->toBeTrue();
});

test('a non super admin cannot deactivate a user', function () {
    $editor = User::factory()->editor()->create();
    $user = User::factory()->editor()->create(['is_active' => true]);

    $this->actingAs($editor)
        ->put(route('admin.users.status.update', $user))
        ->assertForbidden();

    expect($user->fresh()->is_active)->toBeTrue();
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

test('a newly invited user is pending until they change their password', function () {
    $user = User::factory()->editor()->create([
        'is_active' => true,
        'must_change_password' => true,
    ]);

    expect($user->status)->toBe('pending');

    $user->forceFill(['must_change_password' => false])->save();

    expect($user->fresh()->status)->toBe('active');
});

test('a deactivated user has an inactive status regardless of invite state', function () {
    $user = User::factory()->editor()->create([
        'is_active' => false,
        'must_change_password' => true,
    ]);

    expect($user->status)->toBe('inactive');
});

test('the deactivate action only shows for active users', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $pending = User::factory()->editor()->create([
        'is_active' => true,
        'must_change_password' => true,
    ]);

    $response = $this->actingAs($superAdmin)->get(route('admin.users.index'));

    $response->assertSuccessful();
    $response->assertSee(route('admin.users.otp.resend', $pending), false);
    $response->assertDontSee(route('admin.users.status.update', $pending), false);
});

test('users index can be filtered by pending status', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $pending = User::factory()->editor()->create([
        'email' => 'pending@example.com',
        'is_active' => true,
        'must_change_password' => true,
    ]);
    User::factory()->editor()->create([
        'email' => 'active@example.com',
        'is_active' => true,
        'must_change_password' => false,
    ]);

    $this->actingAs($superAdmin)
        ->get(route('admin.users.index', ['status' => 'pending']))
        ->assertSuccessful()
        ->assertSee($pending->email, false)
        ->assertDontSee('active@example.com', false);
});
