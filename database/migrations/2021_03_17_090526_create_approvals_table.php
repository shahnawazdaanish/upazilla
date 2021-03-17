<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
//            $table->uuid("application_id")->index("index_approval_application_id");
//            $table->bigInteger("user_id")->index("index_approved_by");
            $table->uuid("application_id");
            $table->unsignedBigInteger("user_id");
            $table->enum("approval_type", ["APPROVED", "DENIED", "OTHERS"]);
            $table->timestamps();

            $table->unique(["application_id", "user_id"], "unique_approval_application_user");

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('application_id')->references('application_id')->on('applications');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approvals');
    }
}
