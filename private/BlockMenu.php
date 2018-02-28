<?php
/**
 * Class BlockMenu for the exercises of the EWA lecture in SS09
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
 * This class represents the menu, i.e. a list of pizza types
 *
 * @author   Bernhard Kreling, <b.kreling@fbi.h-da.de> 
*/
 
class BlockMenu
{
    // --- ATTRIBUTES ---

    /**
     * Reference to the MySQLi-Database that is
     * accessed by all operations of the class.
     */
    protected $_database = null;
    
    // --- OPERATIONS ---
    
    /**
     * Gets the reference to the DB from the calling page template.
     * Stores the connection in member $_database.
     *
     * @param $database $database is the reference to the DB to be used     
     *
     * @return none
     */
    public function __construct($database) 
    {
        $this->_database = $database;
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is stored in an easily accessible way e.g. as associative array.
     *
     * @return none
     */
    protected function getViewData()
    {
		$Sql = "
			SELECT picture, name, price 
			FROM menu 
			ORDER BY price, name;
		";
		$pizzalist = array();
		$recordset = $this->_database->query ($Sql);
		if ($this->_database->errno) 
		{
		    throw new Exception("BlockMenu::getViewData failed: ".$this->_database->error."\n".$Sql);
		}
		while ($record = $recordset->fetch_assoc()) 
		{
			$pizzalist[] = $record;
		}
		return $pizzalist;
    }
    
    /**
     * Generates an HTML block embraced by a div-tag with the submitted id.
     * If the block contains other blocks this is done recursively.
     *
     * @param $id $id is the unique (!!) id to be used as id in the div-tag     
     *
     * @return none
     */
    public function generateView($id = "") 
    {
        $pizzalist = $this->getViewData();

        if ($id) {
            $id = "id=\"$id\"";
        }
        echo "    <div $id>\n";
        echo "      <table class=\"unbordered\">\n";
		
		for ($i = 0; $i < count($pizzalist); $i++)
		{
			$pizza = $pizzalist[$i];
			$picture = htmlspecialchars($pizza['picture']);
			$name = htmlspecialchars($pizza['name']);
			$price = number_format($pizza['price'], 2, ',', '.');
			echo <<<EOT
        <tr>
          <td class="col0"><img src="img/$picture" alt="Pizza $name" onclick="insertIntoShoppingCart(this)"/></td>
          <td class="col1">$name</td>
          <td class="col2">$price &euro;</td>
        </tr>

EOT;
		}

        echo "      </table>\n";
        echo "    </div>\n";
    }
}
