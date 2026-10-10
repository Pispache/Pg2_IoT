<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('roles', function(Blueprint $t){$t->id();$t->string('name')->unique();$t->string('display_name');$t->string('description')->nullable();$t->timestamps();}); } public function down(): void { Schema::dropIfExists('roles'); } };
