<?php

/**
 * Register custom post type to handle shops and their orders
 */

trait SBWC_OM_CPT
{

    public static function sbwc_om_register_store_cpt()
    {

        /**
         * Post Type: Stores.
         */

        $labels = [
            "name"                     => __("Stores", "sbwc-om"),
            "singular_name"            => __("Store", "sbwc-om"),
            "menu_name"                => __("Stores & Orders", "sbwc-om"),
            "all_items"                => __("Stores & Orders", "sbwc-om"),
            "add_new"                  => __("Add new", "sbwc-om"),
            "add_new_item"             => __("Add new Store", "sbwc-om"),
            "edit_item"                => __("Edit Store", "sbwc-om"),
            "new_item"                 => __("New Store", "sbwc-om"),
            "view_item"                => __("View Store", "sbwc-om"),
            "view_items"               => __("View Stores", "sbwc-om"),
            "search_items"             => __("Search Stores", "sbwc-om"),
            "not_found"                => __("No Stores found", "sbwc-om"),
            "not_found_in_trash"       => __("No Stores found in trash", "sbwc-om"),
            "parent"                   => __("Parent Store: ", "sbwc-om"),
            "featured_image"           => __("Featured image for this Store", "sbwc-om"),
            "set_featured_image"       => __("Set featured image for this Store", "sbwc-om"),
            "remove_featured_image"    => __("Remove featured image for this Store", "sbwc-om"),
            "use_featured_image"       => __("Use as featured image for this Store", "sbwc-om"),
            "archives"                 => __("Store archives", "sbwc-om"),
            "insert_into_item"         => __("Insert into Store", "sbwc-om"),
            "uploaded_to_this_item"    => __("Upload to this Store", "sbwc-om"),
            "filter_items_list"        => __("Filter Stores list", "sbwc-om"),
            "items_list_navigation"    => __("Stores list navigation", "sbwc-om"),
            "items_list"               => __("Stores list", "sbwc-om"),
            "attributes"               => __("Stores attributes", "sbwc-om"),
            "name_admin_bar"           => __("Store", "sbwc-om"),
            "item_published"           => __("Store published", "sbwc-om"),
            "item_published_privately" => __("Store published privately.", "sbwc-om"),
            "item_reverted_to_draft"   => __("Store reverted to draft.", "sbwc-om"),
            "item_scheduled"           => __("Store scheduled", "sbwc-om"),
            "item_updated"             => __("Store updated.", "sbwc-om"),
            "parent_item_colon"        => __("Parent Store:", "sbwc-om"),
        ];

        $args = [
            "label"                 => __("Stores", "sbwc-om"),
            "labels"                => $labels,
            "description"           => "",
            "public"                => true,
            "publicly_queryable"    => false,
            "show_ui"               => true,
            "show_in_rest"          => true,
            "rest_base"             => "",
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "has_archive"           => false,
            "show_in_menu"          => "sbwc-order-manager",
            "show_in_nav_menus"     => true,
            "delete_with_user"      => false,
            "exclude_from_search"   => false,
            "capability_type"       => "post",
            "map_meta_cap"          => true,
            "hierarchical"          => false,
            "rewrite"               => ["slug" => "store", "with_front" => false],
            "query_var"             => true,
            "supports"              => ["title"],
            "show_in_graphql"       => false,
        ];

        register_post_type("store", $args);
    }
}
