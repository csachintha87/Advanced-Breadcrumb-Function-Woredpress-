function advanced_custom_breadcrumbs() {

    if ( is_front_page() ) return;

    global $post;

    echo '<div class="breadcrumbs">';
    echo '<a href="' . home_url() . '">Home</a> » ';

    /* PAGE HIERARCHY */

    if ( is_page() ) {

        if ( $post->post_parent ) {

            $parent_id = $post->post_parent;
            $breadcrumbs = array();

            while ( $parent_id ) {
                $page = get_page( $parent_id );
                $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                $parent_id = $page->post_parent;
            }

            $breadcrumbs = array_reverse($breadcrumbs);

            foreach ( $breadcrumbs as $crumb ) {
                echo $crumb . ' » ';
            }
        }

        echo get_the_title();
    }

    /* SINGLE POSTS */

    elseif ( is_single() ) {

        $post_type = get_post_type();

        /* DEFAULT POSTS */

        if ( $post_type == 'post' ) {

            $categories = get_the_category();

            if ( ! empty( $categories ) ) {

                $category = $categories[0];

                if ( $category->parent != 0 ) {

                    echo get_category_parents( $category->parent, true, ' » ' );
                }

                echo '<a href="' . get_category_link( $category->term_id ) . '">' . $category->name . '</a> » ';
            }

            echo get_the_title();
        }

        /* CUSTOM POST TYPES */

        else {

            $post_type_obj = get_post_type_object( $post_type );

            if ( $post_type_obj ) {

                echo '<a href="' . get_post_type_archive_link( $post_type ) . '">' . $post_type_obj->labels->name . '</a> » ';
            }

            /* CUSTOM TAXONOMY TERMS */

            $taxonomies = get_object_taxonomies( $post_type );

            if ( ! empty( $taxonomies ) ) {

                foreach ( $taxonomies as $taxonomy ) {

                    $terms = get_the_terms( $post->ID, $taxonomy );

                    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {

                        $term = $terms[0];

                        if ( $term->parent ) {

                            echo get_term_parents_list(
                                $term->parent,
                                $taxonomy,
                                array(
                                    'separator' => ' » ',
                                    'link' => true
                                )
                            );
                        }

                        echo '<a href="' . get_term_link( $term ) . '">' . $term->name . '</a> » ';

                        break;
                    }
                }
            }

            echo get_the_title();
        }
    }

    /* TAXONOMY ARCHIVES */

    elseif ( is_tax() || is_category() || is_tag() ) {

        $term = get_queried_object();

        if ( $term->parent ) {

            echo get_term_parents_list(
                $term->parent,
                $term->taxonomy,
                array(
                    'separator' => ' » ',
                    'link' => true
                )
            );
        }

        echo single_term_title('', false);
    }

    /* POST TYPE ARCHIVE */

    elseif ( is_post_type_archive() ) {

        post_type_archive_title();
    }

    /* SEARCH */

    elseif ( is_search() ) {

        echo 'Search results for: ' . get_search_query();
    }

    /* 404 */

    elseif ( is_404() ) {

        echo '404 Not Found';
    }

    echo '</div>';
}
