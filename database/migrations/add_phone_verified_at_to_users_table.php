<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
public function up(): void {
Schema::table('users', function (Blueprint $table) {
if (!Schema::hasColumn('users','telefono')) {
$table->string('telefono',20)->nullable();
}
if (!Schema::hasColumn('users','apellido')) {
$table->string('apellido',100)->nullable();
}
$table->timestamp('phone_verified_at')->nullable();
});
}
public function down(): void {
Schema::table('users', function (Blueprint $table) {
$table->dropColumn(['phone_verified_at']);
});
}
};