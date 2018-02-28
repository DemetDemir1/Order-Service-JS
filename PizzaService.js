//----------------------------------
// Page CustomerOrder
var shoppingcart = null;
function body_onload_CustomerOrder()
{
	shoppingcart = new ShoppingCart('ShoppingCart', 'TotalPrice', 'Address');
}

function nextSiblingElement(node)
{
	do {
		node = node.nextSibling
	} while (node.nodeType != 1);
	return node;
}

function insertIntoShoppingCart(imageNode)
{
	var Name = nextSiblingElement(imageNode.parentNode).firstChild.nodeValue;
	var Price = nextSiblingElement(nextSiblingElement(imageNode.parentNode)).firstChild.nodeValue;
	Price = parseFloat(Price.replace(/,/, '.'));
	shoppingcart.add(Name, Price);
}


//----------------------------------
// Page CustomerStatus, Page Baker, Page Driver
function body_onload_Status()
{
	window.setTimeout('window.location.href = window.location.pathname', 3000);
}

function updateStatus(selection, status)
{
	window.location.href = window.location.pathname + '?id=' + selection + '&status=' + status;
}
