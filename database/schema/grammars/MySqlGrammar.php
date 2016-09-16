<?php

namespace App\Database\Schema\Grammars;

class MySqlGrammar extends \Illuminate\Database\Schema\Grammars\MySqlGrammar {

    /**
     * Create the column definition for an set type.
     *
     * @param  \Illuminate\Support\Fluent  $column
     * @return string
     */
    protected function typeSet(\Illuminate\Support\Fluent $column)
    {
        return "set('".implode("', '", $column->allowed)."')";
    }

}