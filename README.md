This is the API docs for the Hartley's cafe ordering system.

Current API host:
    http://82.10.188.99/Hartleys

Structure requests as:
    http://82.10.188.99/Hartleys/{endpoint}

Required authentication for this version:
    None.

/products/
    /list/
        No additional parameters are required. Will return a list of all the products in the database,
        their prices, and a count of all the products.
    
    /add/
        This endpoint is used to add a product into the database. When adding a product, the request
        should be structured as follows:

            /products/add/product_name/product_price

        The product_price value should be in the format of '12.34'. Do not include a currency symbol.

    /update/
        This endpoint is used to update product details in the database. The request should be
        structured as follows:

            /products/update/update_field/update_value/selector_field/field_value

        The update_field is the detail on the product you wish to change.
        The update_value is the value you wish the update_field to become.
        The selector_field is what will be used to identify the record in the database. /id/ is the simplest.
        The field_value is what will be paired with the selector_field to identify a unique record.

        For example:

            /products/update/product_name/Regular Americano/id/1
            This would update the "product name" for product id 1 to be "Regular Americano".

            /products/update/sold_out/1/id/1
            This would update the product ID 1 to be displayed as "Sold Out" so that no orders can be made
            of the product.

    /delete/
        This endpoint enables the complete removal of a product from the database. Requests should be
        structured as follows:

            /products/delete/product_id
    
/orders/
    /list/
        This endpoint returns a list of all incomplete orders and the list of products they contain.

    /place/
        This endpoint is used for placing an order. The request should be structured as follows:

        /orders/place/customer_name/product_id,product_id,product_id

        So for example, if Joe Bloggs wants to order products 1, 5 and 8, they would request:

        /orders/place/Joe Bloggs/1,5,8

    /complete/
        This endpoint is used to flag an order as completed so that it does not show up in the orders
        list. You will need to supply the id of the order (Which can be obtained by using /list/ endpoint).
        Request should be structured as follows:

        /orders/complete/order_id
