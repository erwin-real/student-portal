<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained();
            $table->foreignId('subject_id')->constrained();
            $table->foreignId('section_id')->constrained();
            $table->string('code');
            $table->string('room');
            $table->time('start_time'); // hh:mm (eg. 14:30)
            $table->time('end_time'); // hh:mm (eg. 15:30)
            $table->string('days'); // CSV (eg. Mon,Tue,Wed,Thu...)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};
