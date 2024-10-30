<?php
/*

This file is part of Cloudy Tags

See the file cloudy-tags.php for plugin information
*/ 

// fades the text color from normal (more tags) to background (fewer tags)
function cloudy_tags_color($weight, $textclr, $shadow_clr, $bgclr) {

    $text_color   = standardize_css_color($textclr);
    $bg_color     = standardize_css_color($bgclr);

    if ($weight) {
        $weight = $weight/100;

        $minr = hexdec(substr($bg_color, 0, 2));
        $ming = hexdec(substr($bg_color, 2, 2));
        $minb = hexdec(substr($bg_color, 4, 2));

        $maxr = hexdec(substr($text_color, 0, 2));
        $maxg = hexdec(substr($text_color, 2, 2));
        $maxb = hexdec(substr($text_color, 4, 2));

        $r = dechex(intval((($maxr - $minr) * $weight) + $minr));
        $g = dechex(intval((($maxg - $ming) * $weight) + $ming));
        $b = dechex(intval((($maxb - $minb) * $weight) + $minb));

        $r = zeropad($r);
        $g = zeropad($g);
        $b = zeropad($b);

        $color = "$r$g$b";
        $color = substr($color,0,6);

        return("color: #$color;");
    }
}

// determines the cloud size, from not much cloud (more tags) to lots of cloud (fewer tags)
function cloudy_tags_shadow($weight, $shadow_clr) {

    $shadow_color = standardize_css_color($shadow_clr);

    if ($weight) {
        $ts_blur = intval(((100 - $weight) * (20)) / 100) + 1;

        return("text-shadow: #$shadow_color 0px 0px {$ts_blur}px;");
    }
}

// Custom get_tags function to support ignoring tags with less than $minnum posts.
function cloudy_tags_get_tags($args = '') {
    extract($args);
    $alltags = get_terms('post_tag', $args);

    $tags = array();

    foreach ($alltags as $tag) {
        if ($tag->count < $minnum || $tag->count > $maxnum)
            continue;
            
        array_push($tags, $tag);
    }

    if (empty($tags)) {
        $return = array();
        return $return;
    }

    $tags = apply_filters('get_tags', $tags, $args);
    return $tags;
}

// Tag cloud function for widget use
function widget_cloudy_tags($args = '') {
    return cloudy_tags('yes', $args);
}

// Tag cloud function for post or page use
function non_widget_cloudy_tags() {
    return cloudy_tags('no');
}

function cloudy_tags($is_widget_yes_no, $args = '') {

    if ($is_widget_yes_no == 'yes') $type = 'widget_cloudy_tags';
    else                            $type = 'non_widget_cloudy_tags';

    $defaults = cloudy_tags_defaults($is_widget_yes_no);
    $options = get_option($type);
    $args = wp_parse_args($args, $options, $defaults);

    extract($args);

    $tags = array();

    if ('yes' == $showtags) {
        $tags = cloudy_tags_get_tags(array_merge($args, array('minnum' => $minnum, 'maxnum' => $maxnum, 'orderby' => 'count', 'order' => 'DESC'))); // Always query top tags
    }

    if ('yes' == $showcats) {
        if ('yes' == $empty) $empty = 0;
        else                 $empty = 1;

        $hide_empty = '&hide_empty='.$empty;
        
        $cats = get_categories("show_count=1&use_desc_for_title=0&hierarchical=0$hide_empty");

        $tagscats = array_merge($tags, $cats);
    } else {
        $tagscats = array_merge($tags);
    }
    
    if (empty($tagscats))
        return;

    $return = generate_tag_cloud($tagscats, $args); // Here's where those top tags get sorted according to $args
    if (is_wp_error($return))
        return false;
    else if (is_array($return)) {
        return $return;
    } else {
        return apply_filters($type, $return, $args);
    }
}


// generate_tag_cloud() - function to create the links for the cloud based on the args from the cloudy_tags() function
// $tagscats = prefetched tag array (get_tags() & get_categories())
// $args['format'] = 'flat' => whitespace separated, 'list' => UL, 'array' => array()
// $args['orderby'] = 'name', 'count', 'rand'
function generate_tag_cloud($tagscats, $args = '') {
    global $wp_rewrite;

    $defaults = cloudy_tags_defaults('yes');

    if ($defaults['widget'] == 'yes') {
        $type = 'widget_cloudy_tags';
        $css_class = 'cloudy-tags-widget';
    } else {
        $type = 'non_widget_cloudy_tags';
        $css_class = 'cloudy-tags-non-widget';
    }

    $options = get_option($type);
    $args = wp_parse_args($args, $options, $defaults);

    extract($args);

    if (!$tagscats)
        return;
    $counts = $tag_links = array();
    if ('rand' == $orderby)
        shuffle($tagscats);
    foreach ((array) $tagscats as $tag) {
        $counts[$tag->name] = $tag->count;
        $cat = $tag->taxonomy;
        if ('category' == $cat) {
            $tag_links[$tag->name] = get_category_link($tag->term_id);
        } else {
            $tag_links[$tag->name] = get_tag_link($tag->term_id);
        }
        if (is_wp_error($tag_links[$tag->name]))
            return $tag_links[$tag->name];
        $tag_ids[$tag->name] = $tag->term_id;
    }

    $min_count = min($counts);
    $spread = max($counts) - $min_count;
    if ($spread <= 0)
        $spread = 1;
    $font_spread = $largest - $smallest;
    if ($font_spread <= 0)
        $font_spread = 1;
    $font_step = $font_spread / $spread;

    // SQL cannot save you; this is a second (potentially different) sort on a subset of data.
    if ('name' == $orderby)
        uksort($counts, 'strnatcasecmp');
    elseif ('count' == $orderby)
        asort($counts);

    if ('DESC' == $order)
        $counts = array_reverse($counts, true);

    $a = array();

    $rel = (is_object($wp_rewrite) && $wp_rewrite->using_permalinks()) ? ' rel="tag"' : '';

    foreach ($counts as $tag => $count) {
        if ($largest == $smallest)
            $tag_weight = $largest;
        else
            $tag_weight = ($smallest + (($count - $min_count) * $font_step));
        $diff = $largest-$smallest;
        if ($diff <= 0)
            $diff = 1;
        if ('yes' == $showcount)
            $postcount = '('.$count.')';
        $color_weight = round(99 * ($tag_weight - $smallest) / ($diff) + 1);
        $cloud_weight = round((99 - $color_weight) * 0.1) + $color_weight;
        $tag_color = cloudy_tags_color($cloud_weight, $textcolor, $shadow, $bground);
        $tag_shadow = cloudy_tags_shadow($color_weight, $shadow);
        if (strlen($tag_color) > 0) {
            $mouse_event = 'onmouseover="this.style.color=\'#' . standardize_css_color($textcolor) . '\';" onmouseout="this.style.color=\'' . substr($tag_color, 7, 7) . '\';"';
        }
        else {
            $mouse_event = '';
        }
        $tag_id = $tag_ids[$tag];
        $tag_link = clean_url($tag_links[$tag]);
        $tag = wp_specialchars($tag);
        if ($format=='list') {
            $a[] = "<li class=\"cloudy-tags-tag-li\"><a href=\"$tag_link\" " .
                   "class=\"$css_class tag-link-$tag_id\" title=\"" .
                   attribute_escape(sprintf(__('%d topics'), $count))."\"$rel style=\"font-size: ".$tag_weight .
                   "$unit; $tag_color;" .
                   "\">$tag</a>".('yes' == $showcount ? " $postcount" : "")."</li>";
        } elseif ($format=='drop') {
            $a[] = "<option value='$tag_link'>$tag".('yes' == $showcount ? " $postcount" : "")."</option>";
        } else {
            $a[] = "<a href=\"$tag_link\" class=\"$css_class tag-link-$tag_id\" " .
                   "title=\"".attribute_escape(sprintf(__('%d topics'), $count)) .
                   "\"$rel style=\"font-size: ".$tag_weight .
                   "$unit; $tag_color $tag_shadow" .
                   "\"$mouse_event>$tag"."</a>".('yes' == $showcount ? " $postcount" : "");
        }
    }

    switch ($format) :
    case 'array' :
        $return =& $a;
        break;
    case 'list' :
        $return = "<ul class='cloudy-tag-cloud'>\n\t";
        $return .= join("\n\t", $a);
        $return .= "\n</ul>\n";
        break;
    case 'drop' :
        $return = "\n<select name=\"cloudy-tags-dropdown\" onchange='document.location.href=this.options[this.selectedIndex].value;'>\n\t<option value=\"\">Select Tag</option>\n\t";
        $return .= join("\n\t", $a);
        $return .= "</option>\n</select>\n";
        break;
    default :
        $return = join("\n", $a);
        break;
    endswitch;

    return apply_filters('generate_tag_cloud', $return, $tagscats, $args);
}

// people may type colors as RGB, #RGB, RRGGBB, or #RRGGBB.
// This function takes that input and always returns RRGGBB
function standardize_css_color($color) {
    if (strlen($color) == 3) {
        $r = substr($color, 0, 1);
        $g = substr($color, 1, 1);
        $b = substr($color, 2, 1);

        $color = "$r$r$g$g$b$b";
    }
    else if (strlen($color) == 4) {
        $r = substr($color, 1, 1);
        $g = substr($color, 2, 1);
        $b = substr($color, 3, 1);

        $color = "$r$r$g$g$b$b";
    }
    else if (strlen($color) == 7) {
        $color = substr($color, 1, 6);
    }
    else { ; /* color shouldn't need to be changed */ }

    return $color;
}

// dec2hex needs an option to set the number of digits.  Until then, we have this . . .
function zeropad($num)
{
    return (strlen($num) == 1) ? '0'.$num : $num;
}

// send some CSS to the HTML head
function get_head($head_option) {
    $style = stripslashes($head_option);
    echo "<style type='text/css'>
    $style
    </style>\n";
}

// Use same set of defaults for widget and non-widget
function cloudy_tags_defaults($is_widget) {
    return(array(
        'title' => 'Tags',
        'smallest' => 12,
        'largest'  => 14,
        'unit' => 'pt',
        'number' => 50,
        'minnum' => 0,
        'maxnum' => 100,
        'format' => 'flat',
        'orderby' => 'name',
        'order' => 'ASC',
        'exclude' => '',
        'include' => '',
        'textcolor' => '#0000FF',
        'shadow'   =>  '#0000FF',
        'bground'  =>  '#FFFFFF',
        'showcount' => 'no',
        'showtags' => 'yes',
        'showcats' => 'yes',
        'empty' => 'no',
        'widget' => $is_widget
    ));
}

function install_defs() {
    $defaults = cloudy_tags_defaults('no');

    add_option('non_widget_cloudy_tags');
    add_option('widget_cloudy_tags');

    update_option('widget_cloudy_tags',$defaults);
    update_option('non_widget_cloudy_tags',$defaults);
}

function uninstall_defs() {
    delete_option('non_widget_cloudy_tags');
    delete_option('widget_cloudy_tags');
}
?>