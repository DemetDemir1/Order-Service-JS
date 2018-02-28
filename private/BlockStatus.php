<?php
/**
 * Class BlockStatus for the exercises of the EWA lecture in SS09
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
 * This class represents a table showing the status of some pizze
 *
 * @author   Bernhard Kreling, <b.kreling@fbi.h-da.de> 
*/
 
class BlockStatus
{
    // --- ATTRIBUTES ---

    /**
     * Reference to the MySQLi-Database that is
     * accessed by all operations of the class.
     */
    protected $_database = null;

    /**
     * array of status value to be selected from the database
     */
	protected $_selectStatus = null;

    /**
     * array of status value to be shown to the user
     */
	protected $_showStatus = null;
    
    /**
     * readonly means "not clickable"
     */
	protected $_readonly = false;
    
    /**
     * the address of the current customer; to be used in a filter condition 
     */
	protected $_customer = '';
    
    /**
     * mapping of status shortcuts to full text
     */
	protected $_statusLong = array(
		'1b' => 'bestellt',
		'2o' => 'im Ofen',
		'3f' => 'fertig',
		'4u' => 'unterwegs',
		'5a' => 'ausgeliefert'
	);

    // --- OPERATIONS ---
    
    /**
     * Gets the reference to the DB from the calling page template.
     * Stores the connection in member $_database.
     *
     * @param $database $database is the reference to the DB to be used     
     *
     * @return none
     */
    public function __construct($database, $selectStatus, $showStatus, $readonly = false) 
    {
        $this->_database = $database;
        $this->_selectStatus = $selectStatus;
        $this->_showStatus = $showStatus;
        $this->_readonly = $readonly;
    }
	
	public function selectCustomer($customer)
	{
		$this->_customer = $customer;
	}

    /**
     * Fetch all data that is necessary for later output.
     * Data is stored in an easily accessible way e.g. as associative array.
	 *
     * @param $orderid is the primary key of an order
     *
     * @return none
     */
    protected function getViewData($orderid = null)
    {
		if ($orderid) 
		{
			$orderid = $this->_database->real_escape_string($orderid);
			$Sql = "
				SELECT orderid as id, Min(`status`) as `status`
				FROM pizza
				WHERE orderid = '$orderid'
				GROUP BY orderid;
			";
		}
		else 
		{
			$selectStatus = implode("','", $this->_selectStatus);
			$customer = $this->_database->real_escape_string($this->_customer);
			$AND_customer = ($customer ? "AND `order`.address = '$customer'" : "");
			$Sql = "
				SELECT pizza.id, pizza.name, pizza.`status`
				FROM pizza INNER JOIN `order` ON pizza.orderid = `order`.id
				WHERE pizza.`status` In ('$selectStatus') $AND_customer
				ORDER BY `order`.`time`, `order`.address, pizza.name, pizza.id;
			";
		}
		$statuslist = array();
		$recordset = $this->_database->query ($Sql);
		if ($this->_database->errno) 
		{
		    throw new Exception("BlockStatus::getViewData failed: ".$this->_database->error."\n".$Sql);
		}
		while ($record = $recordset->fetch_assoc()) 
		{
			$statuslist[] = $record;
		}
		return $statuslist;
    }
    
    /**
     * Generates an HTML block embraced by a div-tag with the submitted id.
     * If the block contains other blocks this is done recursively.
     *
     * @param $id $id is the unique (!!) id to be used as id in the div-tag
     * @param $orderid is the primary key of an order
     *
     * @return none
     */
    public function generateView($id = "", $orderid = null) 
    {
        $statuslist = $this->getViewData($orderid);

        if ($id) {
            $id = "id=\"$id\"";
        }
        echo "    <div $id>\n";
        echo "      <table class=\"unbordered\">\n";
		
		// output table header:
		echo "        <tr>\n";
		if (!$orderid)
			echo "          <th class=\"col0\">&nbsp;</th>\n";
		foreach($this->_showStatus as $statusShort)
		{
			$statusLong = htmlspecialchars($this->_statusLong[$statusShort]);
			echo "          <th>$statusLong</th>\n";
		}
		echo "        </tr>\n";

		// output table data:
		$disabled = ($this->_readonly ? ' disabled="disabled"' : '');
		for ($i = 0; $i < count($statuslist); $i++)
		{
			$pizza = $statuslist[$i];
			$pizzaid = $pizza['id'];
			echo "        <tr>\n";
			if (!$orderid) {
				$name = htmlspecialchars($pizza['name']);
				echo "          <td class=\"col0\">$name</td>\n";
			}
			foreach($this->_showStatus as $statusShort)
			{
				$checked = ($pizza['status']==$statusShort ? ' checked="checked"' : '');
				$onclick = (!$this->_readonly ? " onclick=\"updateStatus('$pizzaid','$statusShort')\"" : "");
				echo "          <td><input type=\"radio\" name=\"p$pizzaid\" value=\"$statusShort\"$disabled$onclick$checked/></td>\n";
			}
			echo "        </tr>\n";
		}

        echo "      </table>\n";
        echo "    </div>\n";
    }
    
    /**
     * Processes the data that comes via GET or POST i.e. CGI.
     * If this block is supposed to do something with submitted
     * data do it here. 
     * If the block contains other blocks do it recursively.
	 *
     * @param $orderid selects the whole order instead of a pizza
     *
     * @return none 
     */
    public function processReceivedData($orderid = false)
    {
		if (!$this->_readonly &&
			isset($_REQUEST['id']) && 
			isset($_REQUEST['status']))
		{
			$field = ($orderid ? 'orderid' : 'id');
			$id = $this->_database->real_escape_string($_REQUEST['id']);
			$status = $this->_database->real_escape_string($_REQUEST['status']);
			$Sql = "UPDATE pizza SET `status`='$status' WHERE `$field`='$id';";
			$this->_database->query($Sql);
			if ($this->_database->errno) 
			{
				throw new Exception("BlockStatus::processReceivedData UPDATE pizza failed: ".$this->_database->error."\n".$Sql);
			}
		}
    }
}
