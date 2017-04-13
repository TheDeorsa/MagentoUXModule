// You don't need to define the same dependencies as the function you are extending EXCEPT jQuery....
define(['jquery'], function($) {
	'use strict';

	return function (customerData) {

        // Extract PID from URL.
        var getIDFromURL = function (productURL) {
            var prodString = String(productURL);
            return prodString.replace(/\/$/, "").substr(prodString.indexOf("product_id/") + 11);
        }

        // Extract Price from String.
        var getPriceFromString = function (priceString) {
            var priceString = String(priceString);
            var priceSpan = priceString.replace(/\/$/, "").substr(priceString.indexOf("£") + 1);
            return priceSpan.replace(/<\/span>\D+[a-z]+>+\D/g, "");
        }

        var unpackData = function (cartData) {
                var items = cartData.items;
                var item;
                var newItems = [];
                var newCart = {};

                // Processing, pick out what we need
                for (var i = 0, len = items.length; i < len; i++) {
                    item = items[i];
                    console.log("items', item");

                    newCart = {
                        "currency" : "GBP",
                        "productId" : getIDFromURL(item.configure_url),
                        "quantity" : String(item.qty),
                        "sku" : String(item.product_sku),
                        "unitPrice" : getPriceFromString(item.product_price)
                    };

                    newItems.push(newCart);

                }

                window.UNI.addCartRows = newItems.reverse();
                dataToMonetate();
        }


	    /** Events listener **/
	    $(document).on('ajaxComplete', function (event, xhr, settings) {
            console.log("our ajax complete");
			if (settings.url.indexOf('cart') != -1 && settings.type.indexOf('GET') != -1) {
                var redirects = ['redirect', 'backUrl'];

                console.log('xhr.responseJSON', xhr.responseJSON);

                if (_.isObject(xhr.responseJSON) && !_.isEmpty(_.pick(xhr.responseJSON, redirects))) {
                    return;
                }

                unpackData(xhr.responseJSON.cart);

        	}
	    });

        // var baseReload = customerData.reload;
        // customerData.reload = function (sectionNames, updateSectionId) {
        //     console.log("reload called (with our override) sectionNames:", sectionNames, "updateSectionId", updateSectionId);
        //     var returnVal = baseReload(sectionNames, updateSectionId);
        //     //newItems.push(get("cart")());
        //
        //     if (sectionNames.indexOf("cart") != -1) {
        //         // console.log("we are going to run");
        //         var cdItems = customerData.get("cart")();
        //         unpackData(cdItems);
        //     }
        //
        //
        //     return returnVal;
        // };

        return customerData;

	};
});
