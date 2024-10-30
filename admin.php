<?php
/*

This file is part of Cloudy Tags

See the file cloudy-tags.php for plugin information
*/ 

function cloudy_tags_options_page() {
    // variables for the field and option names 
    $options = $newoptions = get_option('non_widget_cloudy_tags');
    if ($_POST['cloudy-tags-submit']) {
        $newoptions['number'] = (int) $_POST['cloudy-tags-number'];
        $newoptions['minnum'] = (int) $_POST['cloudy-tags-minnum'];
        $newoptions['maxnum'] = (int) $_POST['cloudy-tags-maxnum'];
        $newoptions['unit'] = $_POST['cloudy-tags-unit'];
        $newoptions['smallest'] = strip_tags(stripslashes($_POST['cloudy-tags-smallest']));
        $newoptions['largest'] = strip_tags(stripslashes($_POST['cloudy-tags-largest']));
        $newoptions['textcolor'] = strip_tags(stripslashes($_POST['cloudy-tags-textcolor']));
        $newoptions['shadow'] = strip_tags(stripslashes($_POST['cloudy-tags-shadow']));
        $newoptions['bground'] = strip_tags(stripslashes($_POST['cloudy-tags-bground']));
        $newoptions['format'] = $_POST['cloudy-tags-format'];
        $newoptions['orderby'] = $_POST['cloudy-tags-orderby'];
        $newoptions['order'] = $_POST['cloudy-tags-order'];
        $newoptions['showcount'] = $_POST['cloudy-tags-showcount'];
        $newoptions['showcats'] = $_POST['cloudy-tags-showcats'];
        $newoptions['showtags'] = $_POST['cloudy-tags-showtags'];
        $newoptions['empty'] = $_POST['cloudy-tags-empty'];

        // Put an options updated message on the screen
        echo('<div class="updated"><p><strong>Cloudy Tags options have been updated</strong></p></div>');
    }

    if ($options != $newoptions) {
        $options = $newoptions;
        update_option('non_widget_cloudy_tags', $options);
    }

    $number = (int) $options['number'];
    $minnum = (int) $options['minnum'];
    $maxnum = (int) $options['maxnum'];
    $unit = $options['unit'];
    $smallest = htmlspecialchars($options['smallest'], ENT_QUOTES);
    $largest = htmlspecialchars($options['largest'], ENT_QUOTES);
    $mincolor = htmlspecialchars($options['bground'], ENT_QUOTES);
    $maxcolor = htmlspecialchars($options['textcolor'], ENT_QUOTES);
    $format = $options['format'];
    $orderby = $options['orderby'];
    $order = $options['order'];
    $showcount = $options['showcount'];
    $showcats = $options['showcats'];
    $showtags = $options['showtags'];
    $empty = $options['empty'];

    // Now display the options editing screen
    echo '<div class="wrap">';
    echo "<h2>Cloudy Tags Options</h2>";
    // options form
?>
        <form method="post" action="<?php echo str_replace('%7E','~',$_SERVER['REQUEST_URI']); ?>">
            <?php wp_nonce_field('update-options') ?>
            <table>

<?php
            AddNonWidgetOptionText($options, 'Tag Text Color #',        'textcolor', 'Color of the links in the cloud.  Please include the #.');
            AddNonWidgetOptionText($options, 'Cloud Color #',           'shadow',   'Color for the cloud/blur/shadow.  Probably should match the Tag Text Color.');
            AddNonWidgetOptionText($options, 'Background Color #',      'bground',  'This does not change the background color.  The cloud fade needs to know what color is behind the text.');

            AddNonWidgetOptionText($options, 'Minimum Font Size',      'smallest', 'This is the size of the smallest tag.');
            AddNonWidgetOptionText($options, 'Maximum Font Size',       'largest',  'This is the size of the largest tag.');

            AddNonWidgetOptionSelect($options, 'Font Display Unit',     'unit',   'What unit to use for font sizes',
                                  array('px' => 'Pixel',
                                        'pt' => 'Point',
                                        'em' => 'Em',
                                        '%' => 'Percent'));

            AddNonWidgetOptionSelect($options, 'Show Tags?',            'showtags', 'Display tags in the cloud',
                                  array('yes' => 'Yes',
                                        'no'  => 'No'));

            AddNonWidgetOptionSelect($options, 'Show Categories?',      'showcats', 'Display categories in the cloud',
                                  array('yes' => 'Yes',
                                        'no'  => 'No'));

            AddNonWidgetOptionSelect($options, 'Show Empty?',           'empty',    'Allow empty categories',
                                  array('no'  => 'No',
                                        'yes' => 'Yes'));

            AddNonWidgetOptionSelect($options, 'Display Post Count?',   'showcount', 'Show number of posts with the tags',
                                  array('no'  => 'No',
                                        'yes' => 'Yes'));

            AddNonWidgetOptionSelect($options, 'Sort By',               'orderby',  'By what field to sort',
                                  array('name'  => 'Name',
                                        'count' => 'Count',
                                        'rand'  => 'Random'));

            AddNonWidgetOptionSelect($options, 'Sort Order',            'order',    'Direction of sort',
                                  array('ASC'  => 'Ascending',
                                        'DESC' => 'Descending'));

            AddNonWidgetOptionText($options, 'Number of Tags to Display',    'number', 'Controls the total number of tags in your cloud.');
            AddNonWidgetOptionText($options, 'Min. Number of Posts',         'minnum', 'Tags with less than this number of posts will not be displayed.');
            AddNonWidgetOptionText($options, 'Max. Number of Posts',         'maxnum', 'Tags with more than this number of posts will not be displayed.');

            AddNonWidgetOptionSelect($options, 'Cloud Format',          'format',   'How to display the cloud.',
                                  array('flat'  => 'Flat',
                                        'list'  => 'List',
                                        'array' => 'Array',
                                        'drop'  => 'Dropdown'));
?>
            </table>
            <p class="submit">
                <input type="submit" name="Submit" value="<?php _e('Update Options »') ?>" />
            </p>
            <input type="hidden" name="cloudy-tags-submit" id="cloudy-tags-submit" value="1" />
        </form>
    </div>

<?php
}

function AddNonWidgetOptionText($options, $title, $item, $description) {

    extract(CloudyTagsCss());
    echo('<tr>
                    <td style="' . $ct_opt_title . '">' . __($title) . '</td>
                    <td style="' . $ct_opt_title . '"><input type="text" id="cloudy-tags-' . $item . '" name="cloudy-tags-' . $item . '" value="' . wp_specialchars($options[$item], true) . '" /></td>
                    <td style="' . $ct_opt_desc  . '">' . __($description) . '</td>
            </tr>');
}

function AddNonWidgetOptionSelect($options, $title, $item, $description, $sel_arr) {

    extract(CloudyTagsCss());
    echo('<tr>
                <td style="' . $ct_opt_title . '">' . __($title) . '</td>
                <td style="' . $ct_opt_title . '">
                    <select id="cloudy-tags-' . $item . '" name="cloudy-tags-' . $item . '">');
    foreach($sel_arr as $key => $select) {
        echo('          <option value="' . $key . '"');
        if ($options[$item] == $key) echo 'selected="selected"';
        echo(">$select</option>");
    }
    echo('
                    </select>
                <td style="' . $ct_opt_desc  . '">' . __($description) . '</td>
            </tr>');
}


function get_head_non_widget() {
    $hover = ".cloudy-tags-shortcode a { text-decoration: none; }  .cloudy-tags-shortcode a:hover { text-decoration: underline; }";
    get_head($hover);
}


function cloudy_tags_add_page() {  // Add a new submenu under Options:
    add_options_page('Cloudy Tags', 'Cloudy Tags', 8, 'cloudytagsoptions', 'cloudy_tags_options_page');
}

add_action('admin_menu', 'cloudy_tags_add_page');

?>