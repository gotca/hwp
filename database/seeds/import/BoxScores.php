<?php

namespace Import;

use App\Database\Seeds\Import\ImportBase;
use Illuminate\Support\Facades\DB;

class BoxScores extends ImportBase
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $players = $this->fetchPlayersByNameKey();

        $statDump = DB::connection('mysql.new')
            ->table('game_stat_dumps')
            ->select('site_id', 'game_id', 'json')
            ->get() ;

        foreach ($statDump as $dump) {
            $json = json_decode($dump->json);

            foreach($json->boxscore as $team => $quarters) {
                foreach($quarters as $quarter => $score) {
                    foreach($score as $nameKey => $goals) {
                        $new = new \StdClass();

                        $new->site_id = $dump->site_id;
                        $new->game_id = $dump->game_id;
                        $new->team = $team == 0 ? 'US' : 'THEM';
                        $new->quarter = $quarter + 1;

                        // account for both teams
                        if (array_key_exists($nameKey, $players)) {
                            $new->player_id = $players[$nameKey];
                            $new->name = '';
                        } else {
                            $new->player_id = 0;
                            $new->name= $nameKey == '_empty_' ? "" : $nameKey;
                        }

                        $new->goals = $goals;


                        DB::connection('mysql.new')
                            ->table('boxscores')
                            ->insert(get_object_vars($new));
                    }
                }
            }
        }
    }

    private function fetchPlayersByNameKey()
    {
        $pdo = DB::connection('mysql.new')->getPdo();
        $stmt = $pdo->query("SELECT name_key, id FROM players");
        $stmt->setFetchMode(\PDO::FETCH_KEY_PAIR);

        return $stmt->fetchAll();
    }


}
