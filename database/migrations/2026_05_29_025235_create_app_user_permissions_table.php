<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * app_user_company = "user has access to this app in this company" (binary on/off)
     * app_user_permissions = "what exactly the user can do inside that app" (granular)
     *
     * Permission keys follow the pattern: {action} or {resource}.{action}
     * Examples: "view", "create", "delete", "invoices.approve", "reports.export"
     */
    public function up(): void
    {
        Schema::create('app_user_permissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('company_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('app_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // e.g. "view", "create", "update", "delete",
            //      "invoices.approve", "reports.export", "salary.view"
            $table->string('permission_key', 100);

            // true = granted, false = explicitly denied (override role defaults)
            $table->boolean('granted')->default(true);

            $table->timestamps();

            $table->unique(
                ['user_id', 'company_id', 'app_id', 'permission_key'],
                'app_user_perm_unique'
            );

            $table->index(['user_id', 'company_id', 'app_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_user_permissions');
    }
};
