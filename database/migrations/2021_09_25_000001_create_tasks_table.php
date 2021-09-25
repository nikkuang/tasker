<?php

use App\Enums\TaskStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('root_parent_id')->nullable()->constrained('tasks')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('tasks')->onDelete('cascade');
            $table->mediumText('parent_hierarchy')->nullable();
            $table->unsignedMediumInteger('order_column')->nullable();
            $table->mediumText('content');
            $table->string('status')->default(TaskStatus::PENDING);
            $table->timestamps();
            $table->softDeletes();

            $table->index('owner_id');
            $table->index('parent_id');
            $table->index('order_column');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
