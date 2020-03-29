<?php

namespace phpboom\services;

use Tracy\Debugger;

/**
 * Služba databáze
 * Umožuje aplikaci připojit se k databázi a volat pomocné metody pro manipulaci s daty
 *
 * Class Db
 * @package phpboom\services
 */
class Db {

    /** @var \mysqli */
    private $connection;

    /**
     * @var int
     */
    private $queryCounter = 0;

    /**
     * Result posledniho provedeneho db dotazu
     * @var \mysqli_result|bool
     */
    private $last;


    /**
     * Vytvori spojeni s databazi a ulozi do vlastnosti $connection
     *
     * @param $host
     * @param $user
     * @param $password
     * @param $database
     */
    public function connection( $host, $user, $password, $database ): void
    {   	
        if (!$this->connection) {
            $this->connection = new \mysqli( $host, $user, $password, $database );
            \mysqli_set_charset($this->connection, 'utf8');

            if (\mysqli_connect_errno()) {
                trigger_error('Chyba při připojování k hostiteli. '.$this->connection->error, E_USER_ERROR);
            } 
        }
    }


    /**
     * Metoda provedení příkazu
     *
     * @param $queryStr
     * @return bool|int
     * @throws \Exception
     */
    public function executeQuery($queryStr)
    {
        Debugger::barDump($queryStr, __FUNCTION__);
        if (!$result = $this->connection->query( $queryStr )) {
            //echo $queryStr;
            //trigger_error('Chyba při provádění dotazu: '.$this->connection->error, E_USER_ERROR);
            $error = 'Chyba při provádění dotazu: '.$this->connection->error;
            Debugger::log($error, 'db-error');
            throw new \Exception($error);
        }

        $this->last = $result;
        return $this->queryCounter++;

    }


    /**
     * Vrati pocet ovlivněných záznamů příkazem
     *
     * @return int
     */
    public function getNumRows(): int
    {
        return $this->last->num_rows;                    
    }


    /**
     * Vrati pole vsech radku ziskanych prikazem
     *
     * @return array|null
     */
    public function getRows(): ?array
    {
        return $this->last->fetch_array(MYSQLI_ASSOC);
    }


    /**
     * Pomocná funkce, která transformuje řádky databáze do PHP pole
     *
     * @return array
     */
    public function getRowsDataArray(): array
    {
        $data = [];
        while($row = $this->getRows()) {
            $data[] = $row;
        }
        return $data;
    }


    /**
     * Metoda odstranění řádků podle podmínky
     *
     * @param $table
     * @param $condition
     * @param $limit
     * @return bool|int
     * @throws \Exception
     */
    public function deleteRecords($table, $condition, $limit)
    {
    	$limits = ( $limit == '' ) ? '' : ' LIMIT ' . $limit;
    	$delete = "DELETE FROM {$table} WHERE {$condition} {$limits}";
    	return $this->executeQuery( $delete );
    }


    /**
     * Metoda aktulizace dat řádků podle podmínky
     *
     * @param $table
     * @param array $changes
     * @param $condition
     * @return bool|int
     * @throws \Exception
     */
    public function updateRecords($table, array $changes, $condition)
    {
    	$update = "UPDATE " . $table . " SET ";
    	foreach( $changes as $field => $value ) {
    		$update .= "`" . $field . "`='{$value}',";
    	}
    	   	
    	// remove last ','
    	$update = substr($update, 0, -1);
    	if ( $condition != '' ) {
            $update .= " WHERE " . $condition;
    	}	
    	return $this->executeQuery( $update );
    }


    /**
     * Metoda pro vložení řádku
     *
     * @param $table
     * @param array $data
     * @return bool|int
     */
    public function insertRecords($table, array $data)
    {
    	// setup some variables for fields and values
    	$fields  = '';
        $values = '';
		
        // sets attributes and values
        foreach ($data as $f => $v) {			
                 $fields .= "`$f`,";
                 $values .= ( is_numeric( $v ) && ( intval( $v ) == $v ) ) ? $v."," : "'$v',";		
        }
		
        // remove last ',' 
    	$setfields = substr($fields, 0, -1);
    	$setvalues = substr($values, 0, -1);
    	
        $insert = "INSERT INTO $table ({$setfields}) VALUES({$setvalues})";
        return $this->executeQuery( $insert );
    }


    /**
     * ID posledniho vlozeneho zaznamu
     *
     * @return mixed
     */
    public function getLastInsertID() 
    {
        return $this->connection->insert_id;
    }


    /**
     * Pocet provedenych prikazu
     *
     * @return int
     */
    public function getQueryCount(): int
    {
        return $this->queryCounter;
    }


    /**
     * Osetreni retezce
     *
     * @param $data
     * @return string
     */
    public function sanitizeData($data): string
    {
    	return $this->connection->real_escape_string($data);
    }
    

    /**
     * Close Connection
     */
    public function __destruct() 
    {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}
