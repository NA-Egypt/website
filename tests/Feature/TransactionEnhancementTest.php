<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionEnhancementTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_user_logs_transaction_with_context_and_sanitizes_password()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
        ];

        // Perform creation via model to trigger observer
        $user = User::create($userData);

        $transaction = Transaction::where('model', 'User')
            ->where('operation', 'create')
            ->first();

        $this->assertNotNull($transaction);
        $this->assertNotNull($transaction->new_values);
        $this->assertEquals('John Doe', $transaction->new_values['name']);
        $this->assertEquals('john@example.com', $transaction->new_values['email']);
        
        // Assert password and other sensitive fields are sanitized
        $this->assertArrayNotHasKey('password', $transaction->new_values);

        // Assert context is recorded
        $this->assertNotNull($transaction->ip_address);
        $this->assertNotNull($transaction->user_agent);
        $this->assertNotNull($transaction->url);
    }

    public function test_updating_user_logs_only_changed_attributes()
    {
        $user = User::create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'password' => 'secret123',
        ]);

        // Clear create transaction to focus on update
        Transaction::truncate();

        // Perform update
        $user->update([
            'name' => 'Updated Name',
        ]);

        $transaction = Transaction::where('model', 'User')
            ->where('operation', 'update')
            ->first();

        $this->assertNotNull($transaction);
        $this->assertEquals(['name' => 'Original Name'], $transaction->old_values);
        $this->assertEquals(['name' => 'Updated Name'], $transaction->new_values);

        // Email was not updated, so it shouldn't be in old_values or new_values
        $this->assertArrayNotHasKey('email', $transaction->old_values);
        $this->assertArrayNotHasKey('email', $transaction->new_values);
    }

    public function test_deleting_user_logs_old_values()
    {
        $user = User::create([
            'name' => 'Delete Me',
            'email' => 'delete@example.com',
            'password' => 'secret123',
        ]);

        Transaction::truncate();

        $user->delete();

        $transaction = Transaction::where('model', 'User')
            ->where('operation', 'delete')
            ->first();

        $this->assertNotNull($transaction);
        $this->assertNotNull($transaction->old_values);
        $this->assertEquals('Delete Me', $transaction->old_values['name']);
        $this->assertArrayNotHasKey('password', $transaction->old_values);
    }
}
