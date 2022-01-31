<?php

/**
 * Backend CSS trait
 */
trait SBWC_OM_CSS
{

    public static function sbwc_om_css()
    { ?>
        <style>
            h2.sbwc-om-admin-title {
                background: white;
                padding: 15px;
                margin-left: -19px;
                margin-top: 0;
                box-shadow: 0px 2px 2px #0000000f;
            }

            input.sbwc-om-store-key,
            input.sbwc-om-store-secret {
                width: 30%;
            }

            button.button.button-primary.button-small.sbwc-om-add-store {
                line-height: 2.5;
                width: 30px;
            }

            button.button.button-secondary.button-small.sbwc-om-rem-store {
                line-height: 2.5;
                width: 30px;
                background: #e30404;
                color: white;
                border-color: #e30404;
            }

            .sbwc-om-store-inputs {
                padding-bottom: 10px;
            }

            #sbwc-om-show-import-info {
                display: inline-block;
                width: 20px;
                height: 20px;
                border: 1px solid;
                text-align: center;
                font-weight: bold;
                text-decoration: none;
                border-radius: 50%;
                margin-left: 5px;
            }

            #sbwc-om-store-settings-curr>tbody>tr>th {
                text-align: left;
                min-width: 140px;
            }

            #sbwc-om-orders-not-retrieved {
                color: #d70000;
                font-size: 13px;
                letter-spacing: 0.5px;
            }

            #sbwc-om-order-lb-overlay {
                width: 100vw;
                height: 100vh;
                position: fixed;
                top: 0;
                left: 0;
                background: #0000008f;
            }

            #sbwc-om-order-lb {
                position: absolute;
                background: white;
                border-radius: 5px;
                border: 2px solid #ccc;
                padding: 30px;
                z-index: 100;
                width: 82vw;
                top: -15%;
            }

            #sbwc-om-order-lb>h3 {
                background: #efefef;
                padding: 10px 30px;
                margin: -30px -30px 5px;
            }

            #sbwc-om-order-lb>h3>a {
                float: right;
                width: 25px;
                height: 25px;
                background: #999;
                color: white;
                border-radius: 50%;
                text-align: center;
                font-weight: normal;
                font-size: 17px;
                margin-right: -15px;
                text-decoration: none;
            }

            #sbwc-om-order-details {
                float: left;
                width: 37%;
            }

            #sbwc-om-order-products {
                float: right;
                width: 61%;
            }

            #sbwc-om-update-order-cont {
                clear: both;
                padding-top: 15px;
            }

            #sbwc-om-order-details-table>tbody>tr>th {
                background: #efefef;
                padding: 10px 15px;
                vertical-align: top;
            }

            #sbwc-om-order-details-table>tbody>tr>td {
                border: 1px solid #ddd;
                padding: 0 20px;
            }

            #sbwc-om-ord-ship {
                padding-top: 10px !important;
                padding-bottom: 10px !important;
            }

            #sbwc-om-order-product-data>thead>tr>th {
                background: #efefef;
                padding: 10px 15px;
                vertical-align: top;
            }

            #sbwc-om-order-product-dataset>tr>td {
                border: 1px solid #ddd;
                padding: 10px 20px;
                text-align: center;
            }

            #sbwc-om-update-error {
                color: #d20101;
                font-style: italic;
                font-weight: 500;
                letter-spacing: 0.5px;
                margin-top: -5px;
            }

            #sbwc-om-ord-ship-tracking {
                font-size: 12px;
            }

            .button.button-primary.sbwc-om-add-ship-co {
                line-height: 2;
                width: 30px;
                height: 25px;
            }

            .button.button-secondary.sbwc-om-rem-ship-co {
                line-height: 2;
                width: 30px;
                height: 25px;
                background: #e30404;
                border-color: #e30404;
                color: white;
            }

            .button.button-primary.sbwc-om-add-ship-co {
                line-height: 2;
                width: 30px;
                height: 25px;
            }

            .button.button-secondary.sbwc-om-rem-ship-co {
                line-height: 2;
                width: 30px;
                height: 25px;
                background: #e30404;
                border-color: #e30404;
                color: white;
            }

            #sbwc-om-ship-co-table>thead>tr>th {
                padding: 10px;
                background: #ddd;
            }

            #sbwc-om-ship-co-table>thead:nth-child(1)>tr:nth-child(1)>th:nth-child(4) {
                background: none;
            }

            #sbwc-om-ship-co-table {
                margin-bottom: 15px;
            }

            #sbwc-om-save-ship-cos {
                position: relative;
                left: 3px;
            }

            #sbwc-om-ship-co-data-row input {
                font-size: 12px;
            }

            #sbwc-om-csv-file-data>h4 {
                background: #efefef;
                padding: 16px;
                margin-top: 30px;
                box-shadow: 0px 2px 2px #ccc;
                position: relative;
            }

            #sbwc-om-schedule-csv-process {
                position: absolute;
                right: 10px;
                top: 9px;
            }

            .sbwc-om-refresh-orders {
                position: relative;
                left: 9px;
                bottom: 6px;
            }

            #sbwc-om-retrieval-timestamp {
                float: right;
            }

            #sbwc-om-refresh-orders-cont {
                background: #f6f7f7;
                padding: 15px 15px 3px;
                margin-bottom: 15px;
                box-shadow: 0px 2px 2px #ccc;
            }

            .ui-state-active,
            .ui-widget-content .ui-state-active,
            .ui-widget-header .ui-state-active,
            a.ui-button:active,
            .ui-button:active,
            .ui-button.ui-state-active:hover {
                border: 1px solid #2271b1 !important;
                background: #2271b1 !important;
            }

            #sbwc-om-log-table>thead>tr>th {
                border: 1px solid #ccc;
                padding: 8px 15px;
                background: white;
            }

            #sbwc-om-log-table>tbody>tr>td:nth-child(1) {
                padding: 8px;
                text-align: center;
                border: 1px solid #ccc;
            }

            #sbwc-om-log-table>tbody>tr>td:nth-child(2) {
                padding: 8px 15px;
                border: 1px solid #ccc;
            }

            #sbwc-om-log-table {
                background: white;
                padding: 5px;
            }

            #sbwc-om-readme-text-cont {
                padding: 10px 30px;
                background: white;
                width: 50%;
                min-width: 360px;
                box-shadow: 0px 2px 2px 2px #d3d3d347;
            }

            #sbwc-om-ship-cos-table>thead>tr>th {
                padding: 7px 15px;
                border: 1px solid #ccc;
            }

            #sbwc-om-ship-cos-table>tbody>tr>td {
                padding: 7px 15px;
                text-align: center;
                border: 1px solid #ccc;
            }

            #sbwc-om-retrieve-ship-cos {
                margin-bottom: 15px;
            }

            #sbwc-om-ord-ship-co {
                width: 350px;
            }

            #sbwc-om-order-details-table {
                width: 100%;
            }
        </style>
<?php }
}

?>