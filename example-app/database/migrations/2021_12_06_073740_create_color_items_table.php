<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColorItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('color_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('hexColor');
            $table->string('layerType');
            $table->json('meta');
            $table->timestamps();
            $table->softDeletes();
            //TODO: add in node indexing for the colors
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('color_items');
    }
}
