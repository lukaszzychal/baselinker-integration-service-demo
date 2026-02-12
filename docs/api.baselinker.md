# Baselinker API Documentation

> **Source:** <https://api.baselinker.com/>
> **Last updated:** 2026-01-27

---

## Introduction

The API enables information exchange between an external system and BaseLinker.
Communication uses data in **JSON format**. To make a request, send a POST request to:

```
https://api.baselinker.com/connector.php
```

### Authorization

A **token** is required for each method. The token is assigned directly to the BaseLinker user account.
Generate your API token in: **Account & other -> My account -> API**.

> **Recommended:** Authorization via HTTP header `X-BLToken`.

### Request Parameters

3 values must be submitted via POST:

| Parameter    | Description                                              |
|-------------|----------------------------------------------------------|
| `token`     | ~~Unique user API key~~ (DEPRECATED, use `X-BLToken`)   |
| `method`    | Name of the requested API method                         |
| `parameters`| Arguments of the requested function in JSON format       |

### Sample Request

```bash
curl 'https://api.baselinker.com/connector.php' \
  -H 'X-BLToken: 1-23-ABC' \
  --data-raw 'method=getOrders&parameters=%7B%22date_from%22%3A+1407341754%7D'
```

### Request Limit

> **100 requests per minute.**

### Encoding Standards

- The API uses **UTF-8**.
- Replace `+` with `%2B` in base64 content before sending.

---

## Product Catalog

| Method | Description |
|--------|-------------|
| `addInventoryPriceGroup` | Create/update a price group in BaseLinker storage |
| `deleteInventoryPriceGroup` | Remove a price group from BaseLinker storage |
| `getInventoryPriceGroups` | Retrieve price groups existing in BaseLinker storage |
| `addInventoryWarehouse` | Add/update a warehouse in BaseLinker inventories |
| `deleteInventoryWarehouse` | Remove a warehouse from BaseLinker inventories |
| `getInventoryWarehouses` | Retrieve list of warehouses (including auto-created for external stocks) |
| `addInventory` | Add/update a BaseLinker catalog |
| `deleteInventory` | Delete a catalog from BaseLinker storage |
| `getInventories` | Retrieve list of catalogs in BaseLinker storage |
| `addInventoryCategory` | Add/update a category in a BaseLinker catalog |
| `deleteInventoryCategory` | Remove categories (products inside removed, subcategories promoted) |
| `getInventoryCategories` | Retrieve category list for a catalog |
| `getInventoryTags` | Retrieve tag list for a catalog |
| `addInventoryManufacturer` | Add/update a manufacturer in a catalog |
| `deleteInventoryManufacturer` | Remove a manufacturer from a catalog |
| `getInventoryManufacturers` | Retrieve manufacturer list for a catalog |
| `getInventoryExtraFields` | Retrieve extra fields for a catalog |
| `getInventoryIntegrations` | List integrations where catalog text values can be overwritten |
| `getInventoryAvailableTextFieldKeys` | List overwritable text fields for a specific integration |
| `addInventoryProduct` | Add/update a product in a BaseLinker catalog |
| `deleteInventoryProduct` | Remove a product from a catalog |
| `getInventoryProductsData` | Retrieve detailed data for selected products |
| `getInventoryProductsList` | Retrieve basic data of chosen products |
| `getInventoryProductsStock` | Retrieve stock data of products |
| `updateInventoryProductsStock` | Update stocks of products/variants (max 1000/request) |
| `getInventoryProductsPrices` | Retrieve gross prices of products |
| `updateInventoryProductsPrices` | Bulk update gross prices (max 1000/request) |
| `getInventoryProductLogs` | Retrieve product change event log |
| `runProductMacroTrigger` | Run personal trigger for product automatic actions |

---

## Inventory Documents

| Method | Description |
|--------|-------------|
| `addInventoryDocument` | Create a new inventory document (as draft) |
| `setInventoryDocumentStatusConfirmed` | Confirm an inventory document (affects stock levels) |
| `getInventoryDocuments` | Retrieve list of inventory documents (supports pagination/filtering) |
| `getInventoryDocumentItems` | Retrieve items for specific/all inventory documents |
| `addInventoryDocumentItems` | Add items to an existing inventory document |
| `getInventoryDocumentSeries` | Retrieve available inventory document series |

---

## Inventory Purchase Orders

| Method | Description |
|--------|-------------|
| `getInventoryPurchaseOrders` | Retrieve list of purchase orders |
| `getInventoryPurchaseOrderItems` | Retrieve items from a specific purchase order |
| `getInventoryPurchaseOrderSeries` | Retrieve purchase order document series |
| `addInventoryPurchaseOrder` | Create a new purchase order (as draft) |
| `addInventoryPurchaseOrderItems` | Add items to an existing purchase order |
| `setInventoryPurchaseOrderStatus` | Change the status of a purchase order |

---

## Inventory Suppliers

| Method | Description |
|--------|-------------|
| `getInventorySuppliers` | Retrieve list of suppliers |
| `addInventorySupplier` | Add/update a supplier |
| `deleteInventorySupplier` | Remove a supplier |

---

## Inventory Payers

| Method | Description |
|--------|-------------|
| `getInventoryPayers` | Retrieve list of payers |
| `addInventoryPayer` | Add/update a payer |
| `deleteInventoryPayer` | Remove a payer |

---

## External Storages

| Method | Description |
|--------|-------------|
| `getExternalStoragesList` | Retrieve list of external storages (shops, wholesalers) |
| `getExternalStorageCategories` | Retrieve category list from an external storage |
| `getExternalStorageProductsData` | Retrieve detailed product data from external storage |
| `getExternalStorageProductsList` | Retrieve product list from external storage |
| `getExternalStorageProductsQuantity` | Retrieve stock from external storage |
| `getExternalStorageProductsPrices` | Retrieve product prices from external storage |
| `updateExternalStorageProductsQuantity` | Bulk update product stock in external storage (max 1000/request) |

---

## Orders

> **Key methods for this integration are marked with bold.**

| Method | Description |
|--------|-------------|
| `getJournalList` | Download order events from last 3 days (requires activation) |
| `addOrder` | Add a new order to BaseLinker |
| `addOrderDuplicate` | Duplicate an existing order |
| **`getOrderSources`** | **Return order source types with IDs (grouped by type: personal, shop, marketplace)** |
| `getOrderExtraFields` | Return extra fields defined for orders |
| **`getOrders`** | **Download orders from a specific date (max 100/request, supports filters)** |
| **`getOrderTransactionData`** | **Retrieve transaction details for a selected order** |
| `getOrdersByEmail` | Search orders by email address |
| `getOrdersByPhone` | Search orders by phone number |
| `deleteOrders` | Delete multiple orders |
| `addInvoice` | Issue an order invoice |
| `addInvoiceCorrection` | Issue an order invoice correction |
| `getInvoices` | Download invoices (max 100/request) |
| `getSeries` | Download invoice/receipt numbering series |
| **`getOrderStatusList`** | **Download order statuses created by the customer** |
| `getOrderPaymentsHistory` | Retrieve payment history for an order |
| `getOrderPickPackHistory` | Retrieve pick pack history for an order |
| `getNewReceipts` | Retrieve receipts waiting to be issued (for fiscal printer integration) |
| `getReceipts` | Retrieve issued receipts (max 100/request) |
| `getReceipt` | Retrieve a single receipt |
| `setOrderFields` | Edit selected fields of an order |
| `addOrderProduct` | Add a new product to an order |
| `setOrderProductFields` | Edit product data within an order |
| `deleteOrderProduct` | Remove a product from an order |
| `setOrderPayment` | Add a payment to an order |
| `setOrderStatus` | Change order status |
| `setOrderStatuses` | Batch set order statuses |
| `setOrderReceipt` | Mark orders with receipt already issued |
| `addOrderInvoiceFile` | Add external invoice file to an order |
| `addOrderReceiptFile` | Add external receipt file to an order |
| `addOrderBySplit` | Create order by splitting products from existing order |
| `setOrdersMerge` | Merge multiple orders into one |
| `getInvoiceFile` | Download invoice file |
| `runOrderMacroTrigger` | Run personal trigger for order automatic actions |
| `getPickPackCarts` | Retrieve list of PickPack carts |

### getOrders -- Recommended Download Flow

1. Set starting date in `date_confirmed_from`
2. Process all received orders (if 100 returned, more may exist)
3. Download next batch using `date_confirmed` from last order (+1 second)
4. Repeat until fewer than 100 orders are returned
5. Save `date_confirmed` of last processed order for next run

> **Tip:** Download only confirmed orders (`get_unconfirmed_orders = false`).
> Unconfirmed orders may be incomplete or change.

---

## Order Returns

| Method | Description |
|--------|-------------|
| `getOrderReturnJournalList` | Download return events from last 3 days |
| `addOrderReturn` | Add a new order return |
| `getOrderReturnExtraFields` | Return extra fields defined for order returns |
| `getOrderReturns` | Download order returns (max 100/request, supports filters) |
| `getOrderReturnStatusList` | Download order return statuses |
| `getOrderReturnPaymentsHistory` | Retrieve payment history for a return |
| `setOrderReturnFields` | Edit selected fields of an order return |
| `addOrderReturnProduct` | Add product to existing order return |
| `setOrderReturnProductFields` | Edit return product data |
| `deleteOrderReturnProduct` | Remove product from a return |
| `setOrderReturnRefund` | Mark return as refunded (no actual money refund) |
| `getOrderReturnReasonsList` | Retrieve list of return reasons |
| `setOrderReturnStatus` | Change return status |
| `setOrderReturnStatuses` | Batch set return statuses |
| `runOrderReturnMacroTrigger` | Run personal trigger for return automatic actions |
| `getOrderReturnProductStatuses` | Retrieve list of return item statuses |

---

## Courier Shipments

| Method | Description |
|--------|-------------|
| `createPackage` | Create a shipment via selected courier |
| `createPackageManual` | Manually enter shipping number and courier name |
| `getCouriersList` | Retrieve list of available couriers |
| `getCourierFields` | Retrieve form fields for creating shipments |
| `getCourierServices` | Retrieve additional courier services (X-press, BrokerSystem, etc.) |
| `getCourierAccounts` | Retrieve list of accounts for a courier |
| `getLabel` | Download shipping label for a shipment |
| `getProtocol` | Download parcel protocol for selected shipments |
| `getCourierDocument` | Download a parcel document |
| `getOrderPackages` | Download shipments created for an order |
| `getPackageDetails` | Get detailed package info (including subpackages) |
| `getCourierPackagesStatusHistory` | Retrieve shipment status history (max 100/request) |
| `deleteCourierPackage` | Delete a previously created shipment |
| `runRequestParcelPickup` | Request parcel pickup for created shipments |
| `getRequestParcelPickupFields` | Retrieve additional fields for pickup request |

---

## Products Storage (OBSOLETE)

> **Warning:** This section is obsolete. Use **Product Catalog** methods instead.
>
> When using external warehouses (`shop_*`, `warehouse_*`), response format may differ from documentation.

| Method | Description |
|--------|-------------|
| `getStoragesList` | Download list of available storages |
| `addCategory` | Add/update category in BaseLinker storage |
| `addProduct` | Add/update product in BaseLinker storage |
| `addProductVariant` | Add/update variant in BaseLinker storage |
| `deleteCategory` | Remove category (products inside removed) |
| `deleteProduct` | Remove product from storage |
| `deleteProductVariant` | Remove product variant from storage |
| `getCategories` | Download category list |
| `getProductsData` | Download detailed product data |
| `getProductsList` | Download product list |
| `getProductsQuantity` | Retrieve stock data |
| `getProductsPrices` | Retrieve product prices |
| `updateProductsQuantity` | Bulk update stock (max 1000/request) |
| `updateProductsPrices` | Bulk update prices (max 1000/request) |

---

## Printouts

| Method | Description |
|--------|-------------|
| `getOrderPrintoutTemplates` | List printout templates for orders |
| `getInventoryPrintoutTemplates` | List printout templates for inventory |

---

## Base Connect

| Method | Description |
|--------|-------------|
| `getConnectIntegrations` | Retrieve list of Base Connect integrations |
| `getConnectIntegrationContractors` | Retrieve contractors for a Base Connect integration |
| `getConnectContractorCreditHistory` | Retrieve contractor trade credit history |
| `setConnectContractorCreditLimit` | Set new trade credit limit for a contractor |
| `addConnectContractorCreditSettlement` | Add manual credit settlement (repayment) for a contractor |
