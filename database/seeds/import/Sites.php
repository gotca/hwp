<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;
use Illuminate\Support\Facades\DB;

class Sites extends ImportBase
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('sites')->insert([
            'domain' => 'hudsonvillewaterpolo'
        ]);
    }
}
