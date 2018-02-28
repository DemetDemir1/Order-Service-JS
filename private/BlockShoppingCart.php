<?php
/**
 * Class BlockShoppingCart for the exercises of the EWA lecture in SS09
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
 * This class represents the shopping cart, i.e. the form containing 
 * the list of selected pizze, the address field and the buttons
 
 * @author   Bernhard Kreling, <b.kreling@fbi.h-da.de> 
*/
 
class BlockShoppingCart
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
     * Generates an HTML block embraced by a div-tag with the submitted id.
     * If the block contains other blocks this is done recursively.
     *
     * @param $id $id is the unique (!!) id to be used as id in the div-tag     
     *
     * @return none
     */
    public function generateView($id = "") 
    {
        if ($id) {
            $id = "id=\"$id\"";
        }
        echo "    <div $id>\n";

		echo <<<EOT
	<form action="CustomerStatus.php" method="post" onsubmit="return shoppingcart.dataOK()">
	  <p>
	  <select id="ShoppingCart" name="ShoppingCart[]" multiple="multiple" size="7">
		<option>Klicken Sie auf eine Pizza</option>
	  </select>
	  </p>
	  <p class="TotalPrice"><span id="TotalPrice">0</span> &euro;</p>
	  <p>
	  <input type="text" class="Address" id="Address" name="Address" value="" />
	  </p>
	  <p>
	  <input type="reset" value="Alle löschen" onclick="shoppingcart.deleteAll()"/>
	  <input type="button" value="Auswahl löschen" onclick="shoppingcart.deleteSelection()"/>
	  </p>
	  <p>
	  <input type="submit" value="Bestellen" />
	  </p>
	</form>

EOT;

        echo "    </div>\n";
    }
    
    /**
     * Processes the data that comes via GET or POST i.e. CGI.
     * If this block is supposed to do something with submitted
     * data do it here. 
     * If the block contains other blocks do it recursively.
     *
     * @return none 
     */
    public function processReceivedData(&$myAddress)
    {
		if (isset($_REQUEST['Address']) && 
			isset($_REQUEST['ShoppingCart']))
		{
			$myAddress = $_REQUEST['Address'];
			$Address = $this->_database->real_escape_string($_REQUEST['Address']);
			$Sql = "
				INSERT INTO `order` (address) 
				VALUES ('$Address');
			";
			$this->_database->query ($Sql);
			if ($this->_database->errno) 
			{
			    throw new Exception("BlockShoppingCart::processReceivedData INSERT order failed: ".$this->_database->error."\n".$Sql);
			}
			$orderid = $this->_database->insert_id;

			$Names = $_REQUEST['ShoppingCart'];
			for ($i = 0; $i < count($Names); $i++) {
				$Name = $this->_database->real_escape_string($Names[$i]);
				$Sql = "
					INSERT INTO pizza (orderid, name, `status`) 
					VALUES ('$orderid', '$Name', '1b');
				";
				$this->_database->query ($Sql);
				if ($this->_database->errno) 
				{
				    throw new Exception("BlockShoppingCart::processReceivedData INSERT pizza failed: ".$this->_database->error."\n".$Sql);
				}
			}
		}
    }
}
