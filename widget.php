<?php
/*

This file is part of Cloudy Tags

See the file cloudy-tags.php for plugin information
*/ 

class CloudyTagsWidget extends WP_Widget {
    function CloudyTagsWidget() {
        $widget_ops = array('classname' => 'CloudyTagsWidget',
                            'description' => 'Tag cloud for your blog tags');
        $control_ops = array('width' => 400);
        $this->WP_Widget('widget_cloudy_tags', 'Cloudy Tags', $widget_ops, $control_ops);
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? __('Tags') : $instance['title']);

        $tagcloud = 'smallest='.$instance['smallest'];
        $tagcloud.= '&largest='.$instance['largest'];
        $tagcloud.= '&textcolor='.$instance['textcolor'];
        $tagcloud.= '&shadow='.$instance['shadow'];
        $tagcloud.= '&bground='.$instance['bground'];
        $tagcloud.= '&unit='.$instance['unit'];
        $tagcloud.= '&format='.$instance['format'];
        $tagcloud.= '&number='.$instance['number'];
        $tagcloud.= '&minnum='.$instance['minnum'];
        $tagcloud.= '&maxnum='.$instance['maxnum'];
        $tagcloud.= '&orderby='.$instance['orderby'];
        $tagcloud.= '&order='.$instance['order'];
        $tagcloud.= '&showcount='.$instance['showcount'];
        $tagcloud.= '&showcats='.$instance['showcats'];
        $tagcloud.= '&showtags='.$instance['showtags'];
        $tagcloud.= '&empty='.$instance['empty'];
        $tagcloud.= '&widget=yes';

        $output = '<div class="cloudy_tags">';
        $output = $output . widget_cloudy_tags($tagcloud);
        $output = $output . '</div>';
        
        // output
        echo $before_widget;
        if($title) echo $before_title.$title.$after_title;
        echo $output;
        echo $after_widget;
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = (int) $new_instance['number'];
        $instance['minnum'] = (int) $new_instance['minnum'];
        $instance['maxnum'] = (int) $new_instance['maxnum'];
        $instance['unit'] = $new_instance['unit'];
        $instance['smallest'] = $new_instance['smallest'];
        $instance['largest'] = $new_instance['largest'];
        $instance['textcolor'] = strip_tags($new_instance['textcolor']);
        $instance['shadow'] = strip_tags($new_instance['shadow']);
        $instance['bground'] = strip_tags($new_instance['bground']);
        $instance['format'] = $new_instance['format'];
        $instance['orderby'] = $new_instance['orderby'];
        $instance['order'] = $new_instance['order'];
        $instance['showcount'] = $new_instance['showcount'];
        $instance['showcats'] = $new_instance['showcats'];
        $instance['showtags'] = $new_instance['showtags'];
        $instance['empty'] = $new_instance['empty'];

        return $instance;
    }

    function form($instance) {
        /* Set up some default widget settings. */
        $defaults = cloudy_tags_defaults('yes');
        $instance = wp_parse_args((array) $instance, $defaults);
?>
    <div style="text-align:center">
        <h3>Cloudy Tags Options</h3>
        <span style="line-height:15px"><br /><br /></span>
        <table>
        <?php
            AddWidgetOptionText($instance, 'Title',                     'title',  'Title shown in sidebar.', $this->get_field_id('title'), $this->get_field_name('title'));

            AddWidgetOptionText($instance, 'Tag Text Color #',        'textcolor', 'Color of the links in the cloud.  Please include the #.', $this->get_field_id('textcolor'), $this->get_field_name('textcolor'));
            AddWidgetOptionText($instance, 'Cloud Color #',           'shadow',   'Color for the cloud/blur/shadow.  Probably should match the Tag Text Color.', $this->get_field_id('shadow'), $this->get_field_name('shadow'));
            AddWidgetOptionText($instance, 'Background Color #',      'bground',  'This does not change the background color.  The cloud fade needs to know what color is behind the text.', $this->get_field_id('bground'), $this->get_field_name('bground'));

            AddWidgetOptionText($instance, 'Minimum Font Size',      'smallest', 'This is the size of the smallest tag.', $this->get_field_id('smallest'), $this->get_field_name('smallest'));
            AddWidgetOptionText($instance, 'Maximum Font Size',       'largest', 'This is the size of the largest tag.', $this->get_field_id('largest'), $this->get_field_name('largest'));

            AddWidgetOptionSelect($instance, 'Font Display Unit',       'unit',   'What unit to use for font sizes',
                                  array('px' => 'Pixel',
                                        'pt' => 'Point',
                                        'em' => 'Em',
                                        '%' => 'Percent'), $this->get_field_id('unit'), $this->get_field_name('unit'));

            AddWidgetOptionSelect($instance, 'Show Tags?',            'showtags', 'Display tags in the cloud',
                                  array('yes' => 'Yes',
                                        'no'  => 'No'), $this->get_field_id('showtags'), $this->get_field_name('showtags'));

            AddWidgetOptionSelect($instance, 'Show Categories?',      'showcats', 'Display categories in the cloud',
                                  array('yes' => 'Yes',
                                        'no'  => 'No'), $this->get_field_id('showcats'), $this->get_field_name('showcats'));

            AddWidgetOptionSelect($instance, 'Show Empty?',           'empty',    'Allow empty categories',
                                  array('no'  => 'No',
                                        'yes' => 'Yes'), $this->get_field_id('empty'), $this->get_field_name('empty'));

            AddWidgetOptionSelect($instance, 'Display Post Count?',   'showcount', 'Show number of posts with the tags',
                                  array('no'  => 'No',
                                        'yes' => 'Yes'), $this->get_field_id('showcount'), $this->get_field_name('showcount'));

            AddWidgetOptionSelect($instance, 'Sort By',               'orderby',  'By what field to sort',
                                  array('name' => 'Name',
                                        'count' => 'Count',
                                        'rand' => 'Random'), $this->get_field_id('orderby'), $this->get_field_name('orderby'));

            AddWidgetOptionSelect($instance, 'Sort Order',            'order',    'Direction of the sort',
                                  array('ASC'  => 'Ascending',
                                        'DESC' => 'Descending'), $this->get_field_id('order'), $this->get_field_name('order'));

            AddWidgetOptionText($instance, 'Number of Tags to Display', 'number', 'Controls the total number of tags in your cloud.', $this->get_field_id('number'), $this->get_field_name('number'));
            AddWidgetOptionText($instance, 'Min. Number of Posts',      'minnum', 'Tags with less than this number of posts will not be displayed.', $this->get_field_id('minnum'), $this->get_field_name('minnum'));
            AddWidgetOptionText($instance, 'Max. Number of Posts',      'maxnum', 'Tags with more than this number of posts will not be displayed.', $this->get_field_id('maxnum'), $this->get_field_name('maxnum'));

            AddWidgetOptionSelect($instance, 'Cloud Format',          'format',   'How to display the cloud.',
                                  array('flat' => 'Flat',
                                        'list' => 'List',
                                        'drop' => 'Dropdown'), $this->get_field_id('format'), $this->get_field_name('format'));

        ?>

        </table>
    </div>
<?php
    }
}

function AddWidgetOptionText($instance, $title, $item, $description, $fieldid, $fieldname) {

    extract(CloudyTagsCss());
    echo('<tr>
                <td style="' . $ct_opt_title . '">' . __($title) . '</td>
                <td style="' . $ct_opt_title . '"><input type="text" id="' . $fieldid . '" name="' . $fieldname . '" value="' . esc_attr($instance[$item]) . '" /></td>
                <td style="' . $ct_opt_desc  . '">' . __($description) . '</td>
            </tr>');
}

function AddWidgetOptionSelect($instance, $title, $item, $description, $sel_arr, $fieldid, $fieldname) {

    extract(CloudyTagsCss());
    echo('<tr>
                <td style="' . $ct_opt_title . '">' . __($title) . '</td>
                <td style="' . $ct_opt_title . '">
                    <select id="' . $fieldid . '" name="' . $fieldname . '">');
    foreach($sel_arr as $key => $select) {
        echo('          <option value="' . $key . '"');
        if ($instance[$item] == $key) echo 'selected="selected"';
        echo(">$select</option>");
    }
    echo('
                    </select>
                <td style="' . $ct_opt_desc  . '">' . __($description) . '</td>
            </tr>');
}


function get_head_widget() {
    $hover = ".CloudyTagsWidget a { text-decoration: none; }  .CloudyTagsWidget a:hover { text-decoration: underline; }";
    get_head($hover);
}


function cloudy_tags_init() {
    register_widget('CloudyTagsWidget');
}

add_action('widgets_init', 'cloudy_tags_init');

?>