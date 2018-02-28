<?php
/**
 * Class CustomerStatus for the exercises of the EWA lecture in SS09
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
require_once './private/BlockShoppingCart.php';
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
class CustomerStatus extends Page
{
    /**
     * Reference to block BlockShoppingCart
     */
	protected $_shoppingcart = null;
	
    /**
     * Reference to block BlockStatus
     */
	protected $_statuscustomer = null;
    
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
		$this->_shoppingcart = new BlockShoppingCart($this->_database);
		$this->_statuscustomer = new BlockStatus(
										$this->_database, 
										array('1b', '2o', '3f', '4u'), 
										array('1b', '2o', '3f', '4u'), 
										true);
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
        $this->generatePageHeader('Status', 'body_onload_Status');
		$this->_statuscustomer->selectCustomer($_SESSION['customer']);
		$this->_statuscustomer->generateView('BlockStatusCustomer');
		echo "    <p><a href=\"CustomerOrder.php\">Neue Bestellung</a></p>\n";
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
		$this->_shoppingcart->processReceivedData($_SESSION['customer']);
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
			session_start();
            $page = new CustomerStatus();
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
CustomerStatus::main();
