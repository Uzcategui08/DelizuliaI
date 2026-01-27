<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('todos')) {
            if (! Schema::hasColumn('todos', 'is_completed')) {
                if (Schema::hasColumn('todos', 'completed')) {
                    DB::statement('ALTER TABLE todos RENAME COLUMN completed TO is_completed');
                } else {
                    Schema::table('todos', function (Blueprint $table) {
                        $table->boolean('is_completed')->default(false);
                    });
                }
            }

            Schema::table('todos', function (Blueprint $table) {
                if (! Schema::hasColumn('todos', 'description')) {
                    $table->text('description')->nullable();
                }

                if (! Schema::hasColumn('todos', 'due_at')) {
                    $table->dateTime('due_at')->nullable();
                }

                if (! Schema::hasColumn('todos', 'reminder_at')) {
                    $table->dateTime('reminder_at')->nullable();
                }

                if (! Schema::hasColumn('todos', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable();
                }
            });

            DB::statement('CREATE INDEX IF NOT EXISTS todos_is_completed_due_at_index ON todos (is_completed, due_at)');

            return;
        }

        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->dateTime('reminder_at')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['is_completed', 'due_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('todos')) {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS todos_is_completed_due_at_index');

        Schema::table('todos', function (Blueprint $table) {
            if (Schema::hasColumn('todos', 'completed_at')) {
                $table->dropColumn('completed_at');
            }

            if (Schema::hasColumn('todos', 'reminder_at')) {
                $table->dropColumn('reminder_at');
            }

            if (Schema::hasColumn('todos', 'due_at')) {
                $table->dropColumn('due_at');
            }
        });

        if (! Schema::hasColumn('todos', 'completed') && Schema::hasColumn('todos', 'is_completed')) {
            DB::statement('ALTER TABLE todos RENAME COLUMN is_completed TO completed');
        } elseif (Schema::hasColumn('todos', 'is_completed') && Schema::hasColumn('todos', 'completed')) {
            Schema::table('todos', function (Blueprint $table) {
                $table->dropColumn('is_completed');
            });
        }
    }
};
