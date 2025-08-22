<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void {
Schema::create('phone_verifications', function (Blueprint $table) {
$table->id();
$table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
$table->string('phone', 20);
$table->string('code', 6);
$table->timestamp('expires_at');
$table->unsignedTinyInteger('attempts')->default(0);
$table->timestamps();
$table->index(['user_id','phone']);
});
}
public function down(): void { Schema::dropIfExists('phone_verifications'); }
};