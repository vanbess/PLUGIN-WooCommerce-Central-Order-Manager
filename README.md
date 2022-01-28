# SBWC Order Manager README

## Overview & Prerequisites

SBWC Order Manager allows the remote management of orders from a single WordPress dashboard. It is to be used in connection with SBWC Order Manager Connect plugin, which needs to be installed on all WooCommerce store websites so that REST requests can be received from the Order Manager dashboard and processed. 

SBWC Background Shipping plugin is also required to be installed, with shipping company details required to be set up on its admin page in order for shipping updates to successfully be processed from SBWC Order Manager via REST request.

In addition, the plugins Sequential Order Numbers Pro and WooCommerce Shipment Tracking needs to be installed as well, since core functions of these plugins are used in the remote shipping update process. If these are not installed, the functionality of the Order Manager plugin will fail.

## Creating and saving store/shop data

Store/shop data is managed via a custom post type called "store". These can be manually added,but they are inserted into the database automatically once a user has set up store/shop connection details under the **Order Manager -> Store Connection Settings** page and saves these settings.

**Please ensure that you have set up remote access credentials under _WooCommerce -> Settings -> Advanced -> REST API page_ and that the store URL, name, client key and client secret is correct, otherwise connection and order retrieval/update processes will fail.**

## Retrieving store/shop order data

Once store connection settings have been saved, each store post/data object can be viewed/edited under the **Order Manager -> Stores & Orders** page by clicking on the store link itself. Once the Edit screen is accessed, there are 2 data tabs presented: Shop Orders and Upload Shipping CSV.

Upon accessing the Shop Orders tab, the user will presented with the opportunity to retrieve the latest 100 orders from the store in question via REST. Note that this is the maximum amount of orders which can initially be retrieved due to there being a limitation of maximum 100 items which can be retrieved per REST request.

Once the Retrieve Orders button is clicked, a request will be sent to the store in question, which will return all orders with a status of "processing". Not other orders will be returned.

If there are no orders available at the time of request, a message confirming this will be displayed. Similarly, the user will be notified if orders with the requisite status or processing was successfully retrieve.

Successfully retrieved orders will be saved as metadata and will consequently be listed under the Shop Orders tab, from where users can check the status of each order. 

Additional details about a specific order such as client, shipping address and order products/items can then be accessed via lightbox by clicking on the View/Edit button for each order. 

Shipping updates for individual orders can also be done here remotely, if needed, and providing that the store in question has shipping company data setup on its side via the SBWC Background Plugin, and has all the requisite plugins installed, as discussed at the top of this document.

The order list can also be refreshed by clicking the Refresh button above the current order list table. This will again fetch the latest 100 orders with a status of "processing" from the store in question and save the order data to said store as required.

## Bulk updating shipping data for a particular store/shop

**Note: initial order retrieval as discussed above is not required in order to be able to bulk update a specific list of orders for a specific store/shop.**

Bulk shipping updates can be done for each store by navigating to the Upload Shipping CSV tab on the store edit screen.

The process works the same as that used in SBWC Background Shipping plugin in that a comma separated CSV file can be uploaded with the requisite data which is to be updated remotely.

Again, the success of this action assumes that you have SBWC Background Shipping, Sequential Order Numbers Pro and WooCommerce Shipment Tracking plugins installed on the store/shop dashboard you wish to update remotely. **If these aren't installed and set up correctly, the update will fail.**

When uploading correctly formatted CSV shipping files for bulk order updates, the filename itself will be renamed to store_name_day_month_year_time.csv. This is done to avoid any potential processing errors due to potential unconventional file naming practices being used in the naming of the CSV file you're uploading. It also allows you to see when the last file was uploaded for processing.

If the shipping update file is successfully uploaded, using the correct format, it will be saved to the server. Once saved, it will be read and its contents displayed below the file upload section so that said contents can be reviewed for accuracy prior to submission.

Once you're satisfied that the content is correct, simply click on the Submit CSV for processing, located at the top right of the shipment data table. A remote update request will now be sent to the store/shop in question, and the order update process will be scheduled via Action Scheduler in the
backend of the store/shop in question.

## Logs

The page **Order Manager -> Log** provides details about the success/failure of order shipping updates scheduled from Order Manager and allows you to determine whether an update has been scheduled to a remote shop successfully.

