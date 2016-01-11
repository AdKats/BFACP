<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class EntrustPivotBfacpUserRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        // Create table for associating roles to users (Many-to-Many)
        Schema::create('bfacp_user_role', function (Blueprint $table) {
            $table->integer('bfacp_user_id')->unsigned();
            $table->foreign('bfacp_user_id')->references('id')->on('bfacp_users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->integer('role_id')->unsigned();
            $table->foreign('role_id')->references('id')->on('bfacp_roles')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['bfacp_user_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::dropIfExists('bfacp_user_role');
    }
}
