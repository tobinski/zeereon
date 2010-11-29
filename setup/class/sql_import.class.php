<?php
//ClassName: SQLImporter
//Import the data stored in a SQL file into a MySql Database
//Author: David Castillo Sanchez - davcs86@gmail.com [d-castillo.info]
//RAC Handler: DrAKkar_eS

class sqlImport {
    var $ErrorDetected = false;
    var $CodigoError;
    var $TextoError;
    function is_comment($text){
        if ($text != ""){
            $fL = $text[0];
            $sL = $text[1];
            switch($fL){
                case "#":
                    $text = "";
                    break;
                case "/":
                    if ($sL == "*")
                        $text = "";
                    break;
                case "-":
                    if ($sL == "-")
                        $text = "";
                    break;
                    
            }
        }
        return $text;
    }
    
    //retrieving the vars
    function sqlImport ($host, $user,$pass, $ArchivoSql) {
    $this -> host = $host;
    $this -> user = $user;
    $this -> pass = $pass;
    $this -> ArchivoSql = $ArchivoSql;
    $this->dbConnect();
    }

    //Connecting to the DB
    function dbConnect () {
        $this->con = @mysql_connect($this -> host, $this -> user, $this -> pass);
    }
    
    //Processing and importing of the SQL statements
    function import () 
    {   
        //if we're connected to DB
           if ($this -> con !== false) 
           {
            //opening and reading the .sql file
            $f = fopen($this -> ArchivoSql,"r+");
            $sqlFile = fread($f, filesize($this -> ArchivoSql));
            // processing and parsing the content
            $sqlFile = str_replace("\r","%BR%",$sqlFile);
            $sqlFile = str_replace("\n","%BR%",$sqlFile);
            $sqlFile = str_replace("%BR%%BR%","%BR%",$sqlFile);
            $sqlArray = explode('%BR%', $sqlFile);
            $sqlArrayToExecute;
            foreach ($sqlArray as $stmt) 
            {
                $stmt = $this->is_comment($stmt);
                if ($stmt != '')
                    $sqlArrayToExecute[] = $stmt;
            }
            $sqlFile = implode("%BR%",$sqlArrayToExecute);
            unset($sqlArrayToExecute);
            $sqlArray = explode(';%BR%', $sqlFile);
            unset($sqlFile);
            //making a loop with all the valid statements
            foreach ($sqlArray as $stmt){
                $stmt = str_replace("%BR%"," ",$stmt);
                $stmt = trim($stmt);
                //$sqlArrayToExecute[] = $stmt;
                // making the query
                $result = mysql_query($stmt,$this->con);
                if (!$result)
                {
                    // if we aren't connected throw an error
                    $this->ErrorDetected = true;
                    $this->CodigoError[] = mysql_errno($this->con);
                    $this->TextoError[] = mysql_error($this->con);
                }
            }
            //print_r($sqlArrayToExecute);
         } else {
         // if we aren't connected throw an error
            $this->ErrorDetected = true;
            $this->CodigoError[] = "1";
            $this->TextoError[] = "MySQL server access denied, please check the access data login";
         }
          
    }//End of importing
    
    //Controlling and displaying the errors on the process
    function ShowErr () 
    {    
           if ($this->ErrorDetected == false)
           {
            $OutPut [0] =  true;
        }else{
            $OutPut [0] =  false;           
            $OutPut [1] = $this->CodigoError;
            $OutPut [2] =  $this->TextoError;
           }

    return $OutPut;
    }
    
}  
?>
