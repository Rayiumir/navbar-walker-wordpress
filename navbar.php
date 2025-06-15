<?php
/**
    * Name: Navbar Walker Wordpress
    * Author: Raymond Baghumian 
    * Author URI: https://rayium.ir
    * Version: 1.0.0
    * License: MIT
*/

class Navbar_Walker extends Walker_Nav_Menu {

    // Start the element (<li> or <a> tag)
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        if ( isset( $args->item_spacing ) && 'preserve' === $args->item_spacing ) {
            $t = "\t";
            $n = "\n";
        } else {
            $t = '';
            $n = '';
        }
        $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

        // Link attributes
        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

        $item_output = '';

        if ($depth === 0) { // Top-level item: <li> with nav-item and potential dropdown
            $classes = empty( $item->classes ) ? array() : (array) $item->classes;
            $classes[] = 'nav-item'; // Add common nav-item class to top-level list items

            // Handle dropdown specific classes for the <li>
            if ( $args->walker->has_children ) {
                $classes[] = 'dropdown';
            }

            $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
            $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

            $id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
            $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

            $output .= $indent . '<li' . $id . $class_names . '>' . $n;

            // Link for top-level item
            $atts['class'] = 'nav-link';
            if ( $args->walker->has_children ) {
                $atts['class'] .= ' dropdown-link';
            }
            $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
            $attributes = '';
            foreach ( $atts as $attr => $value ) {
                if ( ! empty( $value ) ) {
                    $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                    $attributes .= ' ' . $attr . '="' . $value . '"';
                }
            }
            $item_output .= $args->before;
            $item_output .= '<a' . $attributes . '>';
            $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
            $item_output .= '</a>';
            $item_output .= $args->after;

        } else { 
            // Sub-level item (within a dropdown-menu div)
            // Link for sub-level item
            $atts['class'] = 'dropdown-item';
            $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
            $attributes = '';
            foreach ( $atts as $attr => $value ) {
                if ( ! empty( $value ) ) {
                    $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                    $attributes .= ' ' . $attr . '="' . $value . '"';
                }
            }
            $item_output .= $args->before;
            $item_output .= '<a' . $attributes . '>';
            $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
            $item_output .= '</a>';
            $item_output .= $args->after;
        }

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    // Start the children's level (<ul> or <div> for dropdown-menu)
    function start_lvl( &$output, $depth = 0, $args = array() ) {
        if ( isset( $args->item_spacing ) && 'preserve' === $args->item_spacing ) {
            $t = "\t";
            $n = "\n";
        } else {
            $t = '';
            $n = '';
        }
        $indent = str_repeat( $t, $depth );

        // For depth 0 (top-level children), use the dropdown-menu div
        if ($depth === 0) {
            $output .= $n . $indent . '<div class="dropdown-menu">' . $n;
        } else {
            // For deeper levels, use a standard ul with sub-menu class
            $output .= $n . $indent . '<ul class="sub-menu">' . $n;
        }
    }

    // End the children's level (</ul> or </div>)
    function end_lvl( &$output, $depth = 0, $args = array() ) {
        if ( isset( $args->item_spacing ) && 'preserve' === $args->item_spacing ) {
            $t = "\t";
            $n = "\n";
        } else {
            $t = '';
            $n = '';
        }
        $indent = str_repeat( $t, $depth );

        if ($depth === 0) {
            $output .= $indent . '</div>' . $n;
        } else {
            $output .= $indent . '</ul>' . $n;
        }
    }

    // End the element (</li> tag)
    function end_el( &$output, $item, $depth = 0, $args = array() ) {
        if ( isset( $args->item_spacing ) && 'preserve' === $args->item_spacing ) {
            $n = "\n";
        } else {
            $n = '';
        }
        if ($depth === 0) { // Only close <li> for top-level items
            $output .= '</li>' . $n;
        }
        // No closing tag for sub-level items as they are just <a> tags within a div
    }

    // Overriding display_element to customize how children are handled
    // This is crucial to ensure children of depth 0 are rendered directly without <li> wrappers
    function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
        if ( ! $element ) {
            return;
        }

        $id_field = $this->db_fields['id'];
        $id       = $element->$id_field;

        // Display this element (calls start_el)
        parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );

        // If it has children and we are at depth 0, manually call start_lvl and end_lvl
        if ( isset( $children_elements[ $id ] ) && ! empty( $children_elements[ $id ] ) && $depth === 0 ) {
            $this->start_lvl( $output, $depth, $args );
            foreach ( $children_elements[ $id ] as $child ) {
                $this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
            }
            $this->end_lvl( $output, $depth, $args );
            unset( $children_elements[ $id ] ); // Prevent default behavior from also adding <ul>
        }
    }
}

