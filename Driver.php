<?php
/**
 * Class Driver for the exercises of the EWA lecture in SS09
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
 * @Release  1.0 
 * @link     http://www.fbi.h-da.de 
 */

require_once './Page.php';
require_once './private/BlockStatus.php';

/**
 * This is a template for top level classes, which represent 
 * a complete web page and which are called directly by the user.
 * Usually there will only be a single instance of such a class. 
 * The name of the template is supposed
 * to be replaced by the name of the specific XHTML page e.g. baker.
 * The order of methods might correspond to the order of thinking 
 * during implementation.
 
 * @author   Bernhard Kreling, <b.kreling@fbi.h-da.de> 
 */
class Driver extends Page
{
    /**
     * Reference to block BlockStatus
     */
	protected $_statusdriver = null;
    
    /**
     * Instantiates members (to be defined above).   
     * Calls the constructor of the parent i.e. page class.
     * So the database connection is established.
     *
     * @return none
     */
    protected function __construct() 
    {
        parent::__construct();
		$this->_statusdriver = new BlockStatus(
									$this->_database, 
									array('3f', '4u'), 
									array('3f', '4u', '5a'));
    }
    
    /**
     * Cleans up what ever is needed.   
     * Calls the destructor of the parent i.e. page class.
     * So the database connection is closed.
     *
     * @return none
     */
    protected function __destruct() 
    {
        parent::__destruct();
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
			SELECT `order`.id, `order`.address, Min(pizza.`status`) as minstatus, Sum(menu.price) as totalprice
			FROM (menu INNER JOIN pizza ON menu.name = pizza.name)
					INNER JOIN `order` ON pizza.orderid = `order`.id
			WHERE pizza.`status` < '5a'
			GROUP BY `order`.address, `order`.id, `order`.`time`
			HAVING minstatus >= '3f'
			ORDER BY `order`.address, `order`.`time`
		";
		$orderlist = array();
		$recordset = $this->_database->query ($Sql);
		if ($this->_database->errno) 
		{
		    throw new Exception("Driver::getViewData failed: ".$this->_database->error."\n".$Sql);
		}
		while ($record = $recordset->fetch_assoc()) 
		{
			$Sql = "
				SELECT pizza.name 
				FROM pizza INNER JOIN `order` ON pizza.orderid = `order`.id
				WHERE `order`.id = '{$record['id']}'
				ORDER BY pizza.name
			";
			$subrecordset = $this->_database->query ($Sql);
			if ($this->_database->errno) 
			{
			    throw new Exception("Driver::getViewData failed: ".$this->_database->error."\n".$Sql);
			}
			$namelist = '';
			while ($subrecord = $subrecordset->fetch_assoc()) 
			{
				$namelist .= ' '.$subrecord['name'];
			}
			$record['namelist'] = str_replace(' ', ', ', trim($namelist));
			$orderlist[] = $record;
		}
		return $orderlist;
    }
    
    /**
     * First the necessary data is fetched and then the XHTML is 
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if avaialable- the content of 
     * all views contained is generated.
     * Finally the footer is added.
     *
     * @return none
     */
    protected function generateView() 
    {
        $orderlist = $this->getViewData();
        $this->generatePageHeader('Fahrer', 'body_onload_Status');
		foreach($orderlist as $order)
		{
			$address = htmlspecialchars($order['address']);
			$namelist = htmlspecialchars($order['namelist']);
			$totalprice = number_format($order['totalprice'], 2, ',', '.');
			echo "      <div class=\"ListElement\">\n";
			echo "        <h2>$address</h2>\n";
			echo "        <p>$namelist</p>\n";
			echo "        <p>Preis: $totalprice &euro;</p>\n";
			$this->_statusdriver->generateView('BlockOrder'.$order['id'], $order['id']);
			echo "      </div>\n";
		}
        $this->generatePageFooter();
    }
    
    /**
     * Processes the data that comes via GET or POST i.e. CGI.
     * If this page is supposed to do something with submitted
     * data do it here. 
     * If the page contains blocks do it recursively.
     *
     * @return none 
     */
    protected function processReceivedData() 
    {
        parent::processReceivedData();
		$this->_statusdriver->processReceivedData(true);
    }

    /**
     * This main-function has the only purpose to create an instance 
     * of the class and to get all the things going.
     * I.e. the operations of the class are called to produce
     * the output of the XHTML-file.
     * The name "main" is no keyword for php. It is just used to
     * indicate that function as the central starting point.
     * To make it simpler this is a static function. That is you can simply
     * call it without first creating an instance of the class.
     *
     * @return none 
     */    
    public static function main() 
    {
        try {
            $page = new Driver();
            $page->processReceivedData();
            $page->generateView();
        }
        catch (Exception $e) {
            header("Content-type: text/plain; charset=ISO-8859-1");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
Driver::main();
