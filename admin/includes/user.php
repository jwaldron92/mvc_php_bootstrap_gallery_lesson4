<?php
/**
 * Created by PhpStorm.
 * User: jjwInNY
 * Date: 2/13/18
 * Time: 2:17 AM
 */

/**
 * Class User for Database has 
 * @id, 
 * @username
 * @password
 * @firstname
 * @lastname
 * @db_table_fields
 *
 * METHODS
 *  1.find_all_users
 *  2. find_user_by_id
 *  3. find_this_query
 *  4. instantiation($the_record)
 *  5. has_the_attribute($the_attribute)
 *  6. verify_user
 *  7. save
 *  8. create
 *  9. update
 *  10. delete
 *  11. properties
 *  12. clean_properties
 */ 
class User
{

    protected static $db_table = "users";
    protected static $db_table_fields = array('username', 'password', 'first_name', 'last_name');
    public $id;
    public $username;
    public $password;
    public $first_name;
    public $last_name;

    /**
     * Static method
     * @return bool|mysqli_result
     */
    public static function find_all_users()
    {
        return self::find_this_query("SELECT * from users");


        /*
        global $database;
        $result_set = $database->query("SELECT * from users");
        return $result_set;
        */
    }

    /**
     * @param $id the id of the user
     * @return bool|mysqli_result
     */
    public static function find_user_by_id($user_id)
    {
        global $database;
        $the_result_array = self::find_this_query("SELECT * FROM users WHERE id = $user_id LIMIT 1");

        //make sure the array has something else return false
        return !empty($the_result_array) ? array_shift($the_result_array) : false;
//
//        if(!empty($the_result_array)){
//            $first_item = array_shift($the_result_array);
//            return $first_item;
//        } else
//
//
//            return false;
//     }
    }

    /**
     * request database, save object, and user while loop to fetch data, get the result set, and set to variable :)
     * @param $sql
     * @return bool|mysqli_result
     */
    public static function find_this_query($sql)
    {

        global $database;
        $result_set = $database->query($sql);       //request databas
        $the_object_array = array();


        while ($row = mysqli_fetch_array($result_set)) {

            $the_object_array[] = self::instantiation($row);
        }

        return $the_object_array;
    }

    /**
     * @param $found_user the user searched for by id (1-#)
     * @return User the object from mysql database
     */

    public static function instantiation($the_record)
    {

//
//
//
        $the_object = new self();
//        $the_object-> id         = $found_user['id'];
//        $the_object-> username   =  $found_user['username'];
//        $the_object-> password   = $found_user['password'];
//        $the_object-> first_name = $found_user['first_name'];
//        $the_object-> last_name  = $found_user['last_name'];

        foreach ($the_record as $the_attribute => $value) {

            if ($the_object->has_the_attribute($the_attribute)) {

                $the_object->$the_attribute = $value;

            }
        }
        return $the_object;

    } // end of instantion method

    /**
     *
     * get all the attributes form the class
     *
     * use the next function to see if the attribute / parameter is in the array which is in object properties
     * if the varibale is there then return true, else false
     *
     * Assign the object a value in the instantiaion form object php
     *
     * @param $the_attribute get the mysql database
     * see if the key (NOT VALUE EXISTS)
     * return t/F
     */

    private function has_the_attribute($the_attribute)
    {

        $object_properties = get_object_vars($this);

        return array_key_exists($the_attribute, $object_properties);
    }

    public static function verify_user($username, $password)
    {
        /**
         * If useris here, then loghim in
         */
        global $database;

        $username = $database->escape_string($username);

        $password = $database->escape_string($password);

        $sql = "SELECT * from users WHERE";
        $sql .= "username = '{$username}'";
        $sql .= "AND password = '{$password}'";
        $sql .= "LIMIT 1";

        //next return back the actual output

        $sql = "SELECT * FROM users WHERE username = '{$username}' AND password = '{$password}' LIMIT 1 ";

        $the_result_array = self::find_this_query($sql);

        //make sure the array has something else return false
        return !empty($the_result_array) ? array_shift($the_result_array) : false;

    } // END OF verify_user



    public function save() {

        return isset($this->id) ? $this->update() : $this->create();

    } // end of save method


    public function properties(){


        // return
        $properties = array();
        foreach (self::$db_table_fields as $variable) {

            if (property_exists($this, $variable)){

               $properties[$variable] = $this->$variable;
            }


        }
        return $properties;
        //return get_object_vars($this);
    }

    /**
     * CRUD create function for new user registration
     */



    public function create() {
        global $database;

        $properties = $this->clean_properties();          //initialize the variable with all the properties

        //$sql = "INSERT INTO users (username, password, first_name, last_name)";
        //$sql = "INSERT INTO " . self::$db_table . " (username, password, first_name, last_name)";

        $sql = "INSERT INTO " . self::$db_table . " (" . implode(",", array_keys($properties)) . ") ";

//        $sql .= "VALUES ('";
//        $sql .= $database->escape_string($this->username) . "', '";
//        $sql .= $database->escape_string($this->password) . "', '";
//        $sql .= $database->escape_string($this->first_name) . "', '";
//        $sql .= $database->escape_string($this->last_name) . "')";
        //concatenate the keys

        $sql .= "VALUES ('". implode("','", array_values($properties)) . "')";




        //check if the database successfully added the user to the mysql database
        if( $database -> query($sql)) {
            $this->id = $database->the_insert_id();

            return true;

        } else {

            return false;

        }
    } // END OF CREATE METHOD

    /**
     *  Set users with new username, or password
     *
     */

    public function update() {
        global $database;

        $properties = $this->clean_properties() ;

        $properties_pairs = array();

        foreach ($properties as $key => $value) {

            $properties_pairs[] = "{$key}='{$value}'";
        }

        $sql = "UPDATE " .self::$db_table. " SET ";
        $sql .= implode(", ", $properties_pairs);
        $sql .= " WHERE id= " . $database->escape_string($this->id);

        $database->query($sql);

        return (mysqli_affected_rows($database->connection) == 1) ? true : false;


    } // end of update method
//        }
//
//        //$sql = "UPDATE users SET ";
//        $sql = "UPDATE ". self::$db_table . " SET ";
//        $sql .= "username= '" . $database->escape_string($this->username)    . "', ";
//        $sql .= "password= '" . $database->escape_string($this->password)    . "', ";
//        $sql .= "first_name= '" . $database->escape_string($this->first_name). "', ";
//        $sql .= "last_name= '" . $database->escape_string($this->last_name)  . "' ";
//        $sql .= " WHERE id= " . $database->escape_string($this->id);
//
//        $database->query($sql);                 //Taken CARE OF WITH THE QUERY
//
//        return (mysqli_affected_rows($database->connection) == 1) ? true : false;           //if the rows affected are 1 then return true
//
//    } // END OF update methods


    public function delete() {

        global $database;

        $sql = "DELETE FROM ". self::$db_table;
        $sql .= " WHERE id=" . $database->escape_string($this->id);
        $sql .= " LIMIT 1";

        $database->query($sql);

        return (mysqli_affected_rows($database->connection) == 1) ? true : false;

    } //END OF DELETE METHOD
    
    /*
     * pulling out all the properties form this class
     */


    /**
     * just return the array and taking the key and cleaning the value
     * properties with cleaned find the key and clean the properties for whatever reason
     */
    public function clean_properties() {

        global $database;


        $clean_properties = array();

        foreach($this->properties() as $key => $value) {
            $clean_properties[$key] = $database->escape_string($value);

        }
        return $clean_properties;
    }



}
