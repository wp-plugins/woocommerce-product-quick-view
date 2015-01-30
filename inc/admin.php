<?php
if (isset($_POST['qv_save_changes']) && $_POST['qv_save_changes'] == 'Y') { // Check if any new settings are recieved. If yes, update the database.
    $count = 1;
    $text = '';
    if ($_POST['quick_view_text'] != '') {
        $quick_view_text = $_POST['quick_view_text'];
        update_option('quick_view_text', $quick_view_text);
    } else {
        $text = 'error';
        $count++;
    }
    if (isset($_POST['quick_view_color'])) {
        $quick_view_color = $_POST['quick_view_color'];
        update_option('quick_view_color', $quick_view_color);
    } else {
        $count++;
    }
    if (isset($_POST['quick_view_font_color'])) {
        $quick_view_font_color = $_POST['quick_view_font_color'];
        update_option('quick_view_font_color', $quick_view_font_color);
    } else {
        $count++;
    }
    if ($count > 1) {
        ?>
        <div class="updated"> <p><?php _e('Cant be blank.'); ?></p> </div>  
        <?php
    } else {
        ?>
        <div class="updated"> <p><?php _e('Changes saved.'); ?></p> </div>  
        <?php
    }
} else {

    $quick_view_text = get_option('quick_view_text');
    $quick_view_color = get_option('quick_view_color');
    $quick_view_font_color = get_option('quick_view_font_color');
}
?>

<div class="wrap">
    <h2>Quick View Admin Settings.</h2>

    <form name="qv_form" class="qv_form" method="post" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">  

        <input type="hidden" name="qv_save_changes" value="Y">  

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="blogname"><?php _e("Quick View Custom Text: "); ?></label></th>
                    <td>
                        <input type="text" name="quick_view_text" id="quick_view_text" style="<?php if (!empty($text)) { echo 'border: 1px dotted #ff0000;'; } ?>" value="<?php if (!empty($text)) {_e('Quick View'); }else{ echo $quick_view_text;} ?>">
                        <p class="description"><?php if (!empty($text)) { _e('Field cantt be blank'); } else { _e(' Quick View by default'); } ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="blogname"><?php _e("Quick View Button Color: "); ?></label></th>
                    <td>
                        <input class="color {hash:true}" type="text" name="quick_view_color" value="<?php echo $quick_view_color; ?>">
                        <p class="description"><?php _e('#ec4918 by default.'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="blogname"><?php _e("Quick View Font Color: "); ?></label></th>
                    <td>
                        <input class="color {hash:true}" type="text" name="quick_view_font_color" value="<?php echo $quick_view_font_color; ?>">
                        <p class="description"><?php _e('#ffffff by default.'); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">  
            <input type="submit" class="button button-primary" id="submit" name="submit" value="<?php _e('Save Options') ?>" />  
        </p>  
    </form>

</div>