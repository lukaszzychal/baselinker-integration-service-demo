https://api.baselinker.com/

Introduction
The API enables the information exchange between an external system and BaseLinker.
Communication uses data in JSON format. To make a request to the API, send the proper POST request to:
https://api.baselinker.com/connector.php

To send a request for each method a token is required. The token is assigned directly to the BaseLinker user account. User can generate API token in BaseLinker panel in "Account & other -> My account -> API" section.
Authorization with http header (X-BLToken) is recommended.

3 values must be submitted to the connector.php file via POST:
token - unique user API key - DEPRECATED, please use X-BLToken header
method - the name of the requested API method
parameters - arguments of the requested function in JSON format

Sample:
curl 'https://api.baselinker.com/connector.php' -H 'X-BLToken: 1-23-ABC' --data-raw 'method=getOrders&parameters=%7B%22date_from%22%3A+1407341754%7D'

Request limit
The API request limit is 100 requests per minute.


Latest changes
The API was last updated 2026-01-27.


Encoding standards
The API uses UTF-8.
The API expects base64 content in some endpoints and it is very important to replace "+" character with "%2B" sequence before sending it to our API to avoid an incorrect decoding.


Product catalog
addInventoryPriceGroup	The method allows to create a price group in BaseLinker storage. Providing a price group ID will update the existing price group. Such price groups may be later assigned in addInventory method.
deleteInventoryPriceGroup	The method allows you to remove the price group from BaseLinker storage.
getInventoryPriceGroups	The method allows to retrieve price groups existing in BaseLinker storage
addInventoryWarehouse	The method allows you to add a new warehouse available in BaseLinker inventories. Adding a warehouse with the same identifier again will cause updates of the previously saved warehouse. The method does not allow editing warehouses created automatically for the purpose of keeping external stocks of shops, wholesalers etc. Such warehouse may be later used in addInventory method.
deleteInventoryWarehouse	The method allows you to remove the warehouse available in BaseLinker inventories. The method does not allow to remove warehouses created automatically for the purpose of keeping external stocks of shops, wholesalers etc.
getInventoryWarehouses	The method allows you to retrieve a list of warehouses available in BaseLinker inventories. The method also returns information about the warehouses created automatically for the purpose of keeping external stocks (shops, wholesalers etc.)
addInventory	The method allows you to add the BaseLinker catalogs. Adding a catalog with the same identifier again will cause updates of the previously saved catalog.
deleteInventory	The method allows you to delete a catalog from BaseLinker storage.
getInventories	The method allows you to retrieve a list of catalogs available in the BaseLinker storage.
addInventoryCategory	The method allows you to add a category to the BaseLinker catalog. Adding a category with the same identifier again, updates the previously saved category
deleteInventoryCategory	The method allows you to remove categories from BaseLinker warehouse. Along with the category, the products contained therein are removed (however, this does not apply to products in subcategories). The subcategories will be changed to the highest level categories.
getInventoryCategories	The method allows you to retrieve a list of categories for a BaseLinker catalog.
getInventoryTags	The method allows you to retrieve a list of tags for a BaseLinker catalog.
addInventoryManufacturer	The method allows you to add a manufacturer to the BaseLinker catalog. Adding a manufacturer with the same identifier again, updates the previously saved manufacturer
deleteInventoryManufacturer	The method allows you to remove manufacturer from BaseLinker catalog
getInventoryManufacturers	The method allows you to retrieve a list of manufacturers for a BaseLinker catalog.
getInventoryExtraFields	The method allows you to retrieve a list of extra fields for a BaseLinker catalog.
getInventoryIntegrations	The method returns a list of integrations where text values in the catalog can be overwritten. The returned data contains a list of accounts for each integration and a list of languages supported by the integration
getInventoryAvailableTextFieldKeys	The method returns a list of product text fields that can be overwritten for specific integration.
addInventoryProduct	The method allows you to add a new product to BaseLinker catalog. Entering the product with the ID updates previously saved product.
deleteInventoryProduct	The method allows you to remove the product from BaseLinker catalog.
getInventoryProductsData	The method allows you to retrieve detailed data for selected products from the BaseLinker inventory.
getInventoryProductsList	The method allows to retrieve a basic data of chosen products from BaseLinker catalogs.
getInventoryProductsStock	The method allows you to retrieve stock data of products from BaseLinker catalogs.
updateInventoryProductsStock	The method allows to update stocks of products (and/or their variants) in BaseLinker catalog. Maximum 1000 products at a time.
getInventoryProductsPrices	The method allows to retrieve the gross prices of products from BaseLinker inventories.
updateInventoryProductsPrices	The method allows bulk update of gross prices of products (and/or their variants) in the BaseLinker catalog. Maximum 1000 products at a time.
getInventoryProductLogs	The method allows to retrieve a list of events related to product change (or their variants) in the BaseLinker catalog.
runProductMacroTrigger	The method allows you to run personal trigger for products automatic actions.


Inventory documents
addInventoryDocument	The method allows you to create a new inventory document in BaseLinker storage. Documents are created as draft and need to be confirmed by the user or setInventoryDocumentStatusConfirmed API method.
setInventoryDocumentStatusConfirmed	The method allows you to confirm an inventory document, which will affect the stock levels in the warehouse.
getInventoryDocuments	This method allows you to retrieve a list of inventory documents from BaseLinker. It supports pagination and optional filtering by document type, date range, etc.
getInventoryDocumentItems	This method allows you to retrieve document items for specific or for all inventory documents in BaseLinker. In case of a large number of items, pagination is possible.
addInventoryDocumentItems	The method allows you to add items to an existing inventory document.
getInventoryDocumentSeries	This method allows you to retrieve information about available inventory document series in BaseLinker. Each series can be linked to a specific warehouse (warehouse_id) and can have its own numbering format settings.


Inventory purchase orders
getInventoryPurchaseOrders	The method allows you to retrieve a list of purchase orders from BaseLinker storage.
getInventoryPurchaseOrderItems	The method allows you to retrieve items from a specific purchase order.
getInventoryPurchaseOrderSeries	The method allows you to retrieve a list of purchase order document series available in BaseLinker storage.
addInventoryPurchaseOrder	The method allows you to create a new purchase order in BaseLinker storage. Orders are created as drafts by default.
addInventoryPurchaseOrderItems	The method allows you to add items to an existing purchase order.
setInventoryPurchaseOrderStatus	The method allows you to change the status of a purchase order.


Inventory suppliers
getInventorySuppliers	The method allows you to retrieve a list of suppliers available in BaseLinker storage.
addInventorySupplier	The method allows you to add a new supplier or update an existing one in BaseLinker storage.
deleteInventorySupplier	The method allows you to remove a supplier from BaseLinker storage.


Inventory payers
getInventoryPayers	The method allows you to retrieve a list of payers available in BaseLinker storage.
addInventoryPayer	The method allows you to add a new payer or update an existing one in BaseLinker storage.
deleteInventoryPayer	The method allows you to remove a payer from BaseLinker storage.


External storages
getExternalStoragesList	The method allows you to retrieve a list of available external storages (shops, wholesalers) that can be referenced via API.
getExternalStorageCategories	The method allows you to retrieve a category list from an external storage (shop/wholesale) connected to BaseLinker.
getExternalStorageProductsData	The method allows to retrieve detailed data of selected products from an external storage (shop/wholesaler) connected to BaseLinker.
getExternalStorageProductsList	The method allows to retrieve detailed data of selected products from an external storage (shop/wholesaler) connected to BaseLinker.
getExternalStorageProductsQuantity	The method allows to retrieve stock from an external storage (shop/wholesaler) connected to BaseLinker.
getExternalStorageProductsPrices	The method allows to retrieve product prices from an external storage (shop/wholesaler) connected to BaseLinker.
updateExternalStorageProductsQuantity	The method allows to bulk update the product stock (and/or variants) in an external storage (shop/wholesaler) connected to BaseLinker. Maximum 1000 products at a time.


Orders
getJournalList	The method allows you to download a list of order events from the last 3 days. Contact Base support to activate this method on your account. By default it will return empty response.
addOrder	The method allows adding a new order to the BaseLinker order manager.
addOrderDuplicate	The method allows you to add a new order to the BaseLinker order manager by duplicating an existing order. The new order will have the same data as the original order, but with a different ID.
getOrderSources	The method returns types of order sources along with their IDs. Order sources are grouped by their type that corresponds to a field order_source from the getOrders method. Available source types are "personal", "shop", "order_return" or "marketplace_code" e.g. "ebay", "amazon", "ceneo", "emag", "allegro", etc.
getOrderExtraFields	The method returns extra fields defined for the orders. Values of those fields can be set with method setOrderFields. In order to retrieve values of those fields set parameter include_custom_extra_fields in method getOrders
getOrders	The method allows you to download orders from a specific date from the BaseLinker order manager. The order list can be limited using the filters described in the method parameters. A maximum of 100 orders are returned at a time.

It is recommended to download only confirmed orders (get_unconfirmed_orders = false). Unconfirmed orders may be incomplete. The user may be, for example, in the process of creating an order - it already exists in the database, but not all information is completed. Unconfirmed orders may contain only a partial list of products and may be changed soon. Confirmed orders usually do not change anymore and can be safely downloaded to an external system.

The best way to download the ongoing orders is:
Collecting new order identifiers using getJournalList.

Or, using this method:
1. Setting the starting date and specifying it in the date_confirmed_from field
2. Processing of all received orders. If 100 orders are received, there may be even more to download.
3. Downloading the next package of orders by entering the value of the date_confirmed field from last downloaded order in the date_confirmed_from field. In order to avoid downloading the same orders value of date_confirmed should be increased by 1 second. This operation is repeated until you receive a package with less than 100 orders (this means that there are no more orders left to download).
4. Saving the date_confirmed last processed order. You can download orders from this date onwards so that you do not download the same order twice. It is not possible for an order to 'jump' into the database with an earlier confirmation date. This way you can be sure that all confirmed orders have been downloaded.
getOrderTransactionData	The method allows you to retrieve transaction details for a selected order
getOrdersByEmail	The method allows to search for orders related to the given e-mail address. This function is designed to be used in plugins for mail clients (Thunderbird, Outlook, etc.).
getOrdersByPhone	The method allows you to search for orders related to the given phone number. This function is intended for use in caller recognition programs.
deleteOrders	The method allows you to delete multiple orders from the BaseLinker order manager.
addInvoice	The method allows to issue an order invoice.
addInvoiceCorrection	The method allows to issue an order invoice correction.
Either original_invoice_id or return_order_id must be provided.
If return_order_id is provided, all other fields will be ignored (except series_id) and invoice will be created with data from the return order.
If field is optional and not provided, it will be set according to the series settings or default values.
getInvoices	The method allows you to download invoices issued from the BaseLinker order manager. The list of invoices can be limited using filters described in the method parameters. Maximum 100 invoices are returned at a time.
getSeries	The method allows to download a series of invoice/receipt numbering.
getOrderStatusList	The method allows you to download order statuses created by the customer in the BaseLinker order manager.
getOrderPaymentsHistory	The method allows you to retrieve payment history for a selected order, including an external payment identifier from the payment gateway. One order can have multiple payment history entries, caused by surcharges, order value changes, manual payment editing
getOrderPickPackHistory	The method allows you to retrieve pick pack history for a selected order.
getNewReceipts	The method allows you to retrieve receipts waiting to be issued. This method should be used in creating integration with a fiscal printer. The method can be requested for new receipts every e.g. 10 seconds. If any receipts appear in response, they should be confirmed by the setOrderReceipt method after printing to disappear from the waiting list.
getReceipts	The method allows you to retrieve issued receipts. Max 100 receipts are returned at a time. To retrieve a list of new receipts (when integrating a fiscal printer), use the getNewReceipts method.
getReceipt	The method allows you to retrieve a single receipt from the BaseLinker order manager. To retrieve a list of new receipts (when integrating a fiscal printer), use the getNewReceipts method.
setOrderFields	The method allows you to edit selected fields (e.g. address data, notes, etc.) of a specific order. Only the fields that you want to edit should be given, other fields can be omitted in the request.
addOrderProduct	The method allows you to add a new product to your order.
setOrderProductFields	The method allows you to edit the data of selected items (e.g. prices, quantities etc.) of a specific order. Only the fields that you want to edit should be given, the remaining fields can be omitted in the request.
deleteOrderProduct	The method allows you to remove a specific product from the order.
setOrderPayment	The method allows you to add a payment to the order.
setOrderStatus	The method allows you to change order status.
setOrderStatuses	The method allows you to batch set orders statuses
setOrderReceipt	The method allows you to mark orders with a receipt already issued.
addOrderInvoiceFile	The method allows you to add an external file to an invoice previously issued from BaseLinker. It enables replacing a standard invoice from BaseLinker with an invoice issued e.g. in an ERP program.
addOrderReceiptFile	The method allows you to add an external file to a receipt previously issued from BaseLinker. It enables replacing a standard receipt from BaseLinker with a receipt issued e.g. in an ERP program.
addOrderBySplit	Creates a new order by splitting selected products from an existing order. The new order inherits all fields and information from the original one.
setOrdersMerge	Merges multiple orders into one, based on the selected merge mode.
getInvoiceFile	The method allows you to get the invoice file from BaseLinker.
runOrderMacroTrigger	The method allows you to run personal trigger for orders automatic actions.
getPickPackCarts	The method allows you to retrieve a list of all PickPack carts belonging to the authenticated user. The method returns cart details including ID, name, color.


Order returns
getOrderReturnJournalList	The method allows you to download a list of return events from the last 3 days.
addOrderReturn	The method allows adding a new order return to BaseLinker.
getOrderReturnExtraFields	The method returns extra fields defined for order returns. Values of those fields can be set with method setOrderReturnFields. To retrieve values of those fields set parameter include_custom_extra_fields in method getOrderReturns
getOrderReturns	The method allows you to download order returns from a specific date from the BaseLinker return manager. The return list can be limited using the filters described in the method parameters. A maximum of 100 order returns are returned at a time.
getOrderReturnStatusList	The method allows you to download order return statuses created by the customer in the BaseLinker order manager.
getOrderReturnPaymentsHistory	The method allows you to retrieve payment history for a selected order, including an external payment identifier from the payment gateway. One order can have multiple payment history entries, caused by surcharges, order value changes, manual payment editing
setOrderReturnFields	The method allows you to edit selected fields of a specific order return. Only the fields that you want to edit should be given, other fields can be omitted in the request.
addOrderReturnProduct	Add new product to existing order return
setOrderReturnProductFields	The method allows you to edit the data of selected items (e.g. prices, quantities etc.) of a specific order. Only the fields that you want to edit should be given, the remaining fields can be omitted in the request.
deleteOrderReturnProduct	The method allows you to remove a specific product from the return.
setOrderReturnRefund	The method allows you to mark an order return as refunded. Note this method doesn't issue an actual money refund.
getOrderReturnReasonsList	The method returns a list of order return reasons. Values of those fields can be set with method setOrderReturnFields.
setOrderReturnStatus	The method allows you to change order return status.
setOrderReturnStatuses	The method allows you to batch set order return statuses.
runOrderReturnMacroTrigger	The method allows you to run personal trigger for order returns automatic actions.
getOrderReturnProductStatuses	The method returns a list of order return item statuses. Values of those fields can be set with method setOrderReturnFields.


Courier shipments
createPackage	The method allows you to create a shipment in the system of the selected courier.
createPackageManual	The method allows you to enter the shipping number and the name of the courier to the order (function used only to add shipments created outside BaseLinker)
getCouriersList	The method allows you to retrieve a list of available couriers.
getCourierFields	The method allows you to retrieve the form fields for creating shipments for the selected courier.
getCourierServices	The method allows you to retrieve additional courier services, which depend on other shipment settings. Used only for X-press, BrokerSystem, Wysyłam z Allegro, ErliPRO couriers. Not applicable to other couriers whose forms have fixed options. The details of the package should be sent with the method (the format as in createPackage) in order to receive a list of additional services
getCourierAccounts	The method allows you to retrieve the list of accounts connected to a given courier.
getLabel	The method allows you to download a shipping label (consignment) for a selected shipment.
getProtocol	The method allows you to download a parcel protocol for selected shipments if the protocol is available for chosen courier
getCourierDocument	The method allows you to download a parcel document
getOrderPackages	The method allows you to download shipments previously created for the selected order.
getPackageDetails	This method allows to get detailed information about a package. If the package contains multiple subpackages, information about all of them is included in the response.
getCourierPackagesStatusHistory	The method allows you to retrieve the history of the status list of the given shipments. Maximum 100 shipments at a time
deleteCourierPackage	The method allows you to delete a previously created shipment. The method removes the shipment from the BaseLinker system and from the courier system if the courier API allows it
runRequestParcelPickup	The method allows you to request a parcel pickup for previously created shipments. The method sends a parcel pickup request to courier API if the courier API allows it
getRequestParcelPickupFields	The method allows you to retrieve additional fields for a parcel pickup request.


Products storage [OBSOLETE]

When referring to external warehouses (shop_*, warehouse_*) due to different performance of external warehouses, different integration methods and their specific requirements, the response standard may differ from the one described in the documentation.


getStoragesList	This method allows downloading a list of available storages that can be accessed via API.
addCategory	The method allows adding a category to the BaseLinker storage. Adding a category with the same ID again, updates the previously saved category.
addProduct	The method allows you to add a new product to BaseLinker storage. Entering the product with the ID updates previously saved product.
addProductVariant	The method allows to add a new variant to the product in BaseLinker storage. Providing the variant together with the ID, causes an update of the previously saved variant.
deleteCategory	The method allows you to remove categories from BaseLinker storage. Along with the category, the products contained therein are removed (however, this does not apply to products in subcategories). The subcategories will be changed to the highest level categories.
deleteProduct	The method allows you to remove the product from BaseLinker storage.
deleteProductVariant	The method allows you to remove the product from BaseLinker storage.
getCategories	The method allows you to download a list of categories for a BaseLinker storage or a shop storage connected to BaseLinker.
getProductsData	The method allows to download detailed data of selected products from the BaseLinker storage or a shop/wholesaler storage connected to BaseLinker.
getProductsList	The method allows to download detailed data of selected products from the BaseLinker storage or a shop/wholesaler storage connected to BaseLinker.
getProductsQuantity	The method allows to retrieve stock from the BaseLinker storage or the shop/wholesaler storage connected to BaseLinker.
getProductsPrices	The method allows you to fetch prices of products from the BaseLinker storage or the shop/wholesaler storage connected to BaseLinker.
updateProductsQuantity	The method allows to bulk update the product stock (and/or variants) in BaseLinker storage or in a shop/wholesaler storage connected to BaseLinker. Maximum 1000 products at a time.
updateProductsPrices	The method allows for bulk update of product prices (and/or variants) in BaseLinker storage. Maximum 1000 products at a time.


Printouts
getOrderPrintoutTemplates	Returns a list of all configured printout templates available for orders. The output includes technical identifiers used when executing a printout.
getInventoryPrintoutTemplates	Returns a list of all configured printout templates available for inventory (products).


Base Connect
getConnectIntegrations	The method allows you to retrieve a list of all Base Connect integrations on this account
getConnectIntegrationContractors	The method allows you to retrieve a list of contractors connected to the selected Base Connect integration
getConnectContractorCreditHistory	The method allows you to retrieve an information about chosen contractor trade credit history
setConnectContractorCreditLimit	The method allows you to set new trade credit limit for chosen contractor
addConnectContractorCreditSettlement	The method allows you to add a manual credit settlement (repayment) for a chosen contractor in BaseLinker Connect. A settlement reduces the blocked credit amount for the contractor, effectively recording a payment received for previously charged orders. The settlement amount cannot exceed the contractor's currently blocked credit amount (credit_to_pay).

