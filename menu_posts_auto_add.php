<?php
/*
Plugin Name: _ANDYP - Auto add posts to menu
Plugin URI: http://londonparkour.com
Description: Allows you to add posts (of a specific category) to a specific menu automatically.
Version: 1.0
Author: Andy Pearson
Author URI: http://londonparkour.com
*/

// Category (Wiki)
//    --> child category (classes)
//             --> post 1
//             --> post 2
//    --> child category 2 (website)
//             --> post 3
//             --> post 4


add_filter( 'wp_nav_menu_items', 'your_custom_menu_item', 10, 2 );

//  ┌───────────────────────────────────────────┐ 
//  │                                           │░
//  │                Create Menu                │░
//  │                                           │░
//  └───────────────────────────────────────────┘░
//   ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
function your_custom_menu_item( $items, $args ) {

    // Get the menu to add category posts to.
    $menu = wp_get_nav_menu_object($args->menu);

    // Get_field will return the category ID selected. (WIKI)
    $category_id = get_field('category', $menu);

    // If there isn't a linked category, abort.
    if ($category_id == null || $category_id == false || $category_id == ''){ return $items; }

    // get all child categories of parent 
    $child_categories = get_categories(
        array( 'parent' => $category_id )
    );

    // Loop through each child category
    foreach( $child_categories as $count => $child_category ) {
    
        $items .= build_child_category_menu($child_category, $args, $menu, $count);

    }

    return $items;
}


//  ┌───────────────────────────────────────────┐ 
//  │                                           │░
//  │      Build menu from child category       │░
//  │                                           │░
//  └───────────────────────────────────────────┘░
//   ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
function build_child_category_menu($child_category, $args, $menu, $count){

    // Open the child category to menu.
    $current_item = '<li class="menu-item menu-item-level-1 menu-item-'.$count.' menu-item-'. $child_category->slug .'">';

    // Add the child category name.
    $current_item .= $child_category->cat_name ;

        // get list of posts from child category.
        $argument_array = array( 
            'numberposts'       => 5000,
            'category'          => $child_category->cat_ID, 
            'post_type'         =>  'post',

            'order'     => 'ASC',
            'orderby'   => 'order_clause',
            'meta_query' => array(
                 'order_clause' => array(
                      'key' => 'menu_position',
                      'type' => 'NUMERIC' // unless the field is not a number
                 )
            )
        );

       

        $postslist = get_posts( $argument_array );   

        // Check we are using the right menu
        if (is_single() && $args->menu->name == $menu->name) {

            // Build up a list of posts within child category.
            $current_item .= build_list_of_items($postslist);

        }

    // Close the current child category.
    $current_item .= '</li>';

    // Append the current_item to $items.
    return $current_item;
}



//  ┌───────────────────────────────────────────┐ 
//  │                                           │░
//  │           Create list of items            │░
//  │                                           │░
//  └───────────────────────────────────────────┘░
//   ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
function build_list_of_items($list_of_posts){

    // Open a new list.
    $list = '<ul class="sub-menu">';

    // Iterate over each post adding as a new link.
    foreach ($list_of_posts as $count => $post_object) {

        // Build the link
        $list .= build_link($post_object, $count);

    }

    // Close list
    $list .= '</ul>';

    return $list;
}





//  ┌───────────────────────────────────────────┐ 
//  │                                           │░
//  │            Create item in list            │░
//  │                                           │░
//  └───────────────────────────────────────────┘░
//   ░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
function build_link($post_object, $count){

    // Get Icon from post.
    $icon = get_field('material_icon_code', $post_object->ID);
    $icon_string = '';
    if ($icon != ''){
        $icon_string = '<i class="material-icons menu-item-icon">'.$icon.'</i>';
    }

    // Check if link is current page.
    $current_class = '';
    $current_post = str_replace('/','', $_SERVER['REQUEST_URI']);
    if ($post_object->post_name == $current_post){ $current_class = ' menu-item-current '; }

    $relative_url = str_replace( get_site_url() ,'', $post_object->guid );

    // output.
    $link = '<li class="menu-item menu-item-level-2 menu-item-'.$count.' menu-item-'. $post_object->ID .' menu-item-'. $post_object->post_name . $current_class . '">';
        $link .= '<a class="menu-item-link '. $current_class . '" href="' . $relative_url .  '">';
            $link .= $icon_string;
            $link .= $post_object->post_title;
        $link .= '</a>';
    $link .= '</li>';

    return $link;
}