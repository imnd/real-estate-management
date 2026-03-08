<?php

use App\Enums\PremiseStatus;
use App\Enums\PremiseType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('premises', function (Blueprint $table) {
            $table->id();

            $table->foreignId('floor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('number');
            $table->enum('type', PremiseType::getList())->index();

            $table->unsignedSmallInteger('rooms')->default(1);

            $table->decimal('total_area')->unsigned();
            $table->decimal('living_area')->unsigned();
            $table->decimal('kitchen_area')->unsigned();

            $table->enum('status', PremiseStatus::getList())->default(PremiseStatus::Available)->index();

            $table->decimal('base_price', 15)->unsigned();
            $table->decimal('discount_price', 15)->unsigned()->nullable();

            $table->string('plan_image')->nullable()->comment('Path to the layout image');
            $table->json('features')->nullable()->comment('Balcony, view from the window, etc.');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['total_area', 'base_price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('premises');
    }
};
