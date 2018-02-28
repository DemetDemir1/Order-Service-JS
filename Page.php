<?php
ini_set("display_startup_errors", 1); ini_set("display_errors", 1);
ini_set("default_charset", "ISO-8859-1");
/**
 * Class Page for the exercises of the EWA lecture in SS09
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 * 
 * PHP Version 5
 *
 * @category File
 * @package  Pizzaservice
 * @author   Bernhard Kreling, <b.kreling@fbi.h-da.de> 
 * @license  http://www.h-da.de  none 
 * @Release  1.1 
 * @link     http://www.fbi.h-da.de 
 */
 
/**
 * This abstract class is a common base class for all 
 * XHTML-pages to be created. 
 * It manages access to the database and provides operations 
 * for outputting header and footer of a page.
 * Specific pages have to inherit from that class.
 * Each inherited class can use these operations for accessing the db
 * and for creating the generic parts of a XHTML-page.
 *
 * @author   Bernhard Kreling, <b.kreling@fbi.h-da.de> 
 * @author   Ralf Hahn, <ralf.hahn@h-da.de> 
 */ 
abstract class Page
{
    // --- ATTRIBUTES ---

    /**
     * Reference to the MySQLi-Database that is
     * accessed by all operations of the class.
     */
    protected $_database = null;
    
    // --- OPERATIONS ---
    
    /**
     * Connects to DB and stores 
     * the connection in member $_database.  
     * Needs name of DB, user, password.
     *
     * @return none
     */
    protected function __construct() 
    {
		error_reporting(E_ALL);
		require_once './private/pwd.php';
        $this->_database = new MySQLi($host, $user, $password, 'pizzaservice');
		if (mysqli_connect_errno())
		    throw new Exception("Connect to database failed: ".mysqli_connect_error());
    }
    
    /**
     * Closes the DB connection and cleans up
     *
     * @return none
     */
    protected function __destruct()    
    {
		$this->_database->close();
    }
    
    /**
     * Generates the header section of the page.
     * i.e. starting from the content type up to the body-tag.
     * Takes care that all strings passed from outside
     * are converted to safe HTML by htmlspecialchars.
     *
     * @param $headline $headline is the text to be used as title of the page
     * @param $body_onload_function [additional parameter] the name of a JavaScript function to be called with body onload
     *
     * @return none
     */
    protected function generatePageHeader($headline = "", $body_onload_function) 
    {
        $headline = htmlspecialchars($headline, 0, "ISO-8859-1");
        header("Content-type: text/html; charset=ISO-8859-1");
        
		echo <<<EOT
<?xml version="1.0" encoding="ISO-8859-1" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
    <title>Pizzaservice</title>
    <link rel="stylesheet" type="text/css" href="PizzaService.css" />
    <script type="text/javascript" src="ShoppingCart.js"></script>
    <script type="text/javascript" src="PizzaService.js"></script>
  </head>
  <body onload="$body_onload_function()">
    <h1>$headline</h1>

EOT;
    }

    /**
     * Outputs the end of the XHTML-file i.e. /body etc.
     *
     * @return none
     */
    protected function generatePageFooter() 
    {
		echo <<<EOT
  </body>
</html>
EOT;
    }

    /**
     * Processes the data that comes via GET or POST i.e. CGI.
     * If every page is supposed to do something with submitted
     * data do it here. E.g. checking the settings of PHP that
     * influence passing the parameters (e.g. magic_quotes).
     *
     * @return none
     */
    protected function processReceivedData() 
    {
        if (get_magic_quotes_gpc()) {
            throw new Exception
                ("Bitte schalten Sie magic_quotes_gpc in php.ini aus!");
        }
    }
} // end of class

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >