<?php
/**
 * Class CustomerOrder for the exercises of the EWA lecture in SS09
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
require_once './private/BlockMenu.php';
require_once './private/BlockShoppingCart.php';

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
class CustomerOrder extends Page
{
    /**
     * Reference to block BlockMenu
     */
	protected $_menu = null;

    /**
     * Reference to block BlockShoppingCart
     */
	protected $_shoppingcart = null;
    
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
		$this->_menu = new BlockMenu($this->_database);
		$this->_shoppingcart = new BlockShoppingCart($this->_database);
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
        $this->generatePageHeader('Bestellung', 'body_onload_CustomerOrder');
		$this->_menu->generateView('BlockMenu');
		$this->_shoppingcart->generateView('BlockShoppingCart');
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
            $page = new CustomerOrder();
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
CustomerOrder::main();
