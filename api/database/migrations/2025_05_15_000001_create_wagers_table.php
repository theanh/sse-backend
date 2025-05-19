<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('wagers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('total_wager_value');
            $table->unsignedInteger('odds');
            $table->unsignedTinyInteger('selling_percentage');
            $table->decimal('selling_price', 10, 2);
            $table->decimal('current_selling_price', 10, 2);
            $table->decimal('percentage_sold', 5, 2)->nullable();
            $table->decimal('amount_sold', 10, 2)->nullable();
            $table->timestamp('placed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wagers');
    }
};
