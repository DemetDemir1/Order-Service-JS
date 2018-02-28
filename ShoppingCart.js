/* public constructor */ function ShoppingCart(idShoppingCart, idTotalPrice, idAddress)
{
	this.SelectedNames = document.getElementById(idShoppingCart);
	this.SelectedPrices = new Array();
	this.TotalPriceValue = 0;
	this.TotalPrice = document.getElementById(idTotalPrice).firstChild;	// ist ein Textnode
	this.Address = document.getElementById(idAddress);	// ist ein HTML Input
	
	this.deleteAll = ShoppingCart_deleteAll;
	this.deleteSelection = ShoppingCart_deleteSelection;
	this.add = ShoppingCart_add;
	this.dataOK = ShoppingCart_dataOK;
	this.showTotalPrice = ShoppingCart_showTotalPrice;

	this.deleteAll();
}

/* public */ function ShoppingCart_deleteAll()
{
	while (this.SelectedNames.length > 0)
		this.SelectedNames.remove(this.SelectedNames.length-1);
	this.SelectedPrices = new Array();
	this.TotalPriceValue = 0;
	this.showTotalPrice();
}

/* public */ function ShoppingCart_deleteSelection()
{
	var i = 0;
	var p = 0;
	var newArray = new Array();
	while (i < this.SelectedNames.length) {
		if (this.SelectedNames.options[i].selected) {
			this.SelectedNames.remove(i);
			this.TotalPriceValue -= this.SelectedPrices[p];
		}
		else {
			newArray[i] = this.SelectedPrices[p];
			i++;
		}
		p++;
	}
	this.SelectedPrices = newArray;
	this.showTotalPrice();
}

/* public */ function ShoppingCart_add(Name, Price)
{
	this.SelectedNames.appendChild(new Option(Name));
	this.SelectedPrices[this.SelectedPrices.length] = Price;
	this.TotalPriceValue += Price;
	this.showTotalPrice();
}

/* public */ function ShoppingCart_dataOK()
{
	var ok1 = (this.SelectedPrices.length > 0);
	if (!ok1)
		alert ('Bitte klicken Sie auf eine Pizza!');
	var ok2 = this.Address.value.length > 0;
	if (!ok2)
		alert ('Bitte geben Sie Ihre Adresse ein!');
	
	for (i = 0; i < this.SelectedNames.length; i++)
		this.SelectedNames.options[i].selected = true;

	return ok1 && ok2;
}

/* private */ function ShoppingCart_showTotalPrice()
{
	var sum = this.TotalPriceValue.toFixed(2).toString().replace(/\./, ',');
	this.TotalPrice.nodeValue = sum.replace(/-/, '');	// eventuelles negatives Vorzeichen entfernen
}
