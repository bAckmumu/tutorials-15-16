<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class DBHandler
{
    const TABLE_ALBUMS = 'albums';
    var $connection;

    /**
     * @param $host String host to connect to.
     * @param $user String username to use with the connection. Make sure to grant all necessary privileges.
     * @param $password String password belonging to the username.
     * @param $db String name of the database.
     */
    function __construct($host,$user,$password,$db){

        //TODO create the database connection.
        $this->connection = new mysqli($host,$user,$password,$db);
        // Check connection
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
        
        //TODO make sure the table 'albums' exists.
        $this->ensureAlbumsTable();
    }

    /**
     * creates a database record for the given artist and title.
     * @param $artistName String name of te album's artist
     * @param $albumTitle String title of the album
     * @return bool true for success, false for error
     */
    function insertAlbum($artistName,$albumTitle){
        if($this->connection){
            // TODO insert the album via mysqli
            
            $this->sanitizeInput($artistName);
            $this->sanitizeInput($albumTitle);

            // note: there is a question mark at the end (wildcard)
            $query = "INSERT INTO ". self::TABLE_ALBUMS ." (artist, title) VALUES (?, ?)";

            // prepare the statement.
            // the returned object is a "statement object"
            // http://php.net/manual/de/mysqli.prepare.php
            if($statement = $this->connection->prepare($query)) { // assuming $mysqli is the connection
                // the first parameter indicates that $name is a String.
                $statement->bind_param("ss", $artistName, $albumTitle);

                // now, execute the query
                $statement->execute();
                // any additional code you need would go here.
                return true;
            } else {
                $error = $this->connection->errno . ' ' . $this->connection->error;
                echo $error; // 1054 Unknown column 'foo' in 'field list'
            }
        }
        return false;
    }

    /**
     * makes sure that the albums table is present in the database
     * before any interaction occurs with it.
     */
    function ensureAlbumsTable(){
        if($this->connection){
            // TODO create table if it doesn't exist.
            // sql to create table

            // Select 1 from table_name will return false if the table does not exist.
            $checkSql = "SELECT 1 FROM ". self::TABLE_ALBUMS ." LIMIT 1";

            if($this->connection->query($checkSql) !== FALSE)
            {
               return;
            }

            // sql to create table
            $sql = "CREATE TABLE ". self::TABLE_ALBUMS ." (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
            artist VARCHAR(30) NOT NULL,
            title VARCHAR(30) NOT NULL
            )";

            if ($this->connection->query($sql) === TRUE) {
                echo "Table ". self::TABLE_ALBUMS ." created successfully";
            } else {
                echo "Error creating table: " . $this->connection->error;
            }

            /*// note: there is a question mark at the end (wildcard)
            $query = "CREATE TABLE ? (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
            artist VARCHAR(30) NOT NULL,
            title VARCHAR(30) NOT NULL,
            )";

            // prepare the statement.
            // the returned object is a "statement object"
            // http://php.net/manual/de/mysqli.prepare.php
            $statement = $this->connection->prepare($query);
            
            // the first parameter indicates that $name is a String.
            $statement->bind_param("s", TABLE_ALBUMS);

            // now, execute the query
            if ($statement->execute() === TRUE) {
                echo "Table MyGuests created successfully";
            } else {
                echo "Error creating table: " . $this->connection->error;
            }*/


        }
    }

    /**
     * @return array of rows (id, artist, title)
     */
    function fetchAlbums(){
        $albums = array();

        // TODO fetch all albums and put them into the $albums array.
        if($this->connection){
            // note: there is a question mark at the end (wildcard)
            $query = "SELECT id, artist,title FROM ". self::TABLE_ALBUMS;


            // prepare the statement.
            // the returned object is a "statement object"
            // http://php.net/manual/de/mysqli.prepare.php
            $statement = $this->connection->prepare($query);

            // now, execute the query
            $statement->execute();

            // the results need to be bound to variables.
            // make sure to match the order in the query!
            $statement->bind_result($id, $artist, $title);

            // fetch the actual values;
            while($statement->fetch()){
                $albums[] = array($id, $artist, $title);
            }
        }
        return $albums;
    }

    /**
     * useful to sanitize data before trying to insert it into the database.
     * @param $string String to be escaped from malicious SQL statements
     */
    function sanitizeInput(&$string){
        $string = $this->connection->real_escape_string($string);
    }
}