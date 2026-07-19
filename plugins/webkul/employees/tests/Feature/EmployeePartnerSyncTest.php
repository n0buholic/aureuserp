<?php

use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Partner;

require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('syncs the manager partner when creating an employee partner', function () {
    Partner::factory()->create();

    $manager = Employee::factory()->create();

    expect($manager->partner_id)->not->toBe($manager->id);

    $employee = Employee::factory()->create([
        'parent_id' => $manager->id,
    ])->fresh('partner');

    expect($employee->partner)->not->toBeNull()
        ->and($employee->partner->parent_id)->toBe($manager->partner_id);
});

it('syncs the manager partner when updating an employee manager', function () {
    Partner::factory()->create();

    $manager = Employee::factory()->create();
    $employee = Employee::factory()->create();

    $employee->update([
        'parent_id' => $manager->id,
    ]);

    $employee = $employee->fresh('partner');

    expect($manager->partner_id)->not->toBe($manager->id)
        ->and($employee?->partner)->not->toBeNull()
        ->and($employee?->partner?->parent_id)->toBe($manager->partner_id);
});
