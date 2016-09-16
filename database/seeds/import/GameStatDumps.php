<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;
use Illuminate\Support\Facades\DB;

class GameStatDumps extends ImportBase
{
    public function run()
    {
        $oldData = DB::connection('mysql.old')
            ->table('game')
            ->select('game_id', 'json_dump', 'dump_version')
            ->whereNotNull('json_dump')
            ->get();

        foreach ($oldData as $data) {
            $this->addSiteToData($data);
            $this->rename($data, 'json_dump', 'json');

            if ($data->dump_version == "1") {
                $data->json = $this->convertToNewVersion($data->json);
            }

            unset($data->dump_version);

            DB::connection('mysql.new')
                ->table('game_stat_dumps')
                ->insert(get_object_vars($data));
        }
    }

    /**
     * Dump v1 has the opponent box score as an array of digits containing just the total for the quarter,
     * we need it to be an array of objects where [ [{ cap => goals }, {cap=>goals} ...], [...] ]  
     * 
     * @param $jsonString string
     * @return string
     */
    function convertToNewVersion($jsonString)
    {
        $obj = json_decode($jsonString);
        $quarters = [];
        
        foreach($obj->boxscore[1] as $k => $v) {
            if ($v === 0) {
                $quarters[] = new \StdClass();
            } else {
                $quarters[] = [null => $v];
            }
        }

        $obj->boxscore[1] = $quarters;
        return json_encode($obj);
    }
}
