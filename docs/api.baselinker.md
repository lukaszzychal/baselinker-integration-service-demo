# Baselinker API Documentation (Local Copy)
Source: https://api.baselinker.com/
Date: 2026-02-11

## API Methods

The following methods were extracted from the documentation page:

- **getOrders**: Downloads orders from a specific date. Recommended for downloading confirmed orders.
- **addOrder**: Adds a new order to the BaseLinker order manager.
- **getOrderTransactionData**: Retrieves transaction details for a selected order.
- **getJournalList**: Downloads a list of order events from the last 3 days.
- **setOrderFields**: Edits selected fields of a specific order.
- **addInvoice**: Issues an order invoice.
- **getOrderStatusList**: Downloads order statuses created by the customer.
- **getInventoryProductsData**: Retrieves detailed data for selected products from the BaseLinker inventory.
- **getInventoryProductsList**: Downloads a list of products from the inventory.
- **getInventoryProductLogs**: Retrieves logs for a product.
- **addInventoryProduct**: Adds a new product to the inventory.
- **deleteInventoryProduct**: Deletes a product from the inventory.
- **getInventories**: Retrieves a list of inventories.
- **getInventoryCategories**: Retrieves a list of categories.
- **addInventoryCategory**: Adds a category.
- **deleteInventoryCategory**: Deletes a category.
- **getInventoryTags**: Retrieves a list of tags.
- **addInventoryTag**: Adds a tag.
- **deleteInventoryTag**: Deletes a tag.
- **getInventoryManufacturers**: Retrieves a list of manufacturers.
- **addInventoryManufacturer**: Adds a manufacturer.
- **deleteInventoryManufacturer**: Deletes a manufacturer.
- **getInventoryWarehouses**: Retrieves a list of warehouses.
- **addInventoryWarehouse**: Adds a warehouse.
- **deleteInventoryWarehouse**: Deletes a warehouse.
- **getInventoryPriceGroups**: Retrieves price groups.
- **addInventoryPriceGroup**: Adds a price group.
- **deleteInventoryPriceGroup**: Deletes a price group.

## Important Note

**`getOrderDetails` does not exist.**
To get details of an order, use `getOrders` with `order_id` or `orders_ids` parameter.

## General Info

Communication with the Baselinker API uses JSON format.
Endpoint: `https://api.baselinker.com/connector.php`
POST Request required with `method` and `parameters` (JSON).
Limit: 100 requests per minute.
Output: JSON.
