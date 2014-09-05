<form method="post" id="fonts-form" class="form-horizontal">
    <div class="col-md-12">
        <div class="col-md-8">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <td>&nbsp;</td>
                        <td>Font</td>
                        <td>Type</td>
                        <td><?php echo Language::getVar('SUMO_NOUN_SIZE')?></td>
                        <td><?php echo Language::getVar('SUMO_NOUN_UPPERCASE')?></td>
                    </tr>
                </tbody>
                <tbody>
                    <tr>
                        <td>Body</td>
                        <td>
                            <select name="fonts[body][name]" class="form-control">
                                <?php foreach ($fonts as $name) {
                                    echo '<option' . (isset($settings['fonts']['body']['name']) && $settings['fonts']['body']['name'] == $name ? ' selected' : '') . '>' . $name . '</option>';
                                } ?>
                            </select>
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_COLORS_HEADINGS')?></td>
                        <td>
                            <select name="fonts[headings][name]" class="form-control">
                                <?php foreach ($fonts as $name) {
                                    echo '<option' . (isset($settings['fonts']['headings']['name']) && $settings['fonts']['headings']['name'] == $name ? ' selected' : '') . '>' . $name . '</option>';
                                } ?>
                            </select>
                        </td>
                        <td>
                            <select name="fonts[headings][type]" class="form-control">
                                <option value="normal" <?php echo (isset($settings['fonts']['headings']['type']) && $settings['fonts']['headings']['type'] == 'normal' ? ' selected' : '') ?>><?php echo Language::getVar('SUMO_NOUN_FONT_NORMAL')?></option>
                                <option value="italic" <?php echo (isset($settings['fonts']['headings']['type']) && $settings['fonts']['headings']['type'] == 'italic' ? ' selected' : '') ?>><?php echo Language::getVar('SUMO_NOUN_FONT_ITALIC')?></option>
                            </select>
                        </td>
                        <td>&nbsp;</td>
                        <td>
                            <select name="fonts[headings][uppercase]" class="form-control">
                                <option value="0" <?php echo (isset($settings['fonts']['headings']['uppercase']) && !$settings['fonts']['headings']['uppercase'] ? ' selected' : '') ?>><?php echo Language::getVar('NO')?></option>
                                <option value="1" <?php echo (isset($settings['fonts']['headings']['uppercase']) && $settings['fonts']['headings']['uppercase'] ? ' selected' : '') ?>><?php echo Language::getVar('YES')?></option>
                            </select> 
                        </td>
                    </tr>
                    <tr>
                        <td>Menu</td>
                        <td>
                            <select name="fonts[menu][name]" class="form-control">
                                <?php foreach ($fonts as $name) {
                                    echo '<option' . (isset($settings['fonts']['menu']['name']) && $settings['fonts']['menu']['name'] == $name ? ' selected' : '') . '>' . $name . '</option>';
                                } ?>
                            </select>
                        </td>
                        <td>
                            <select name="fonts[menu][type]" class="form-control">
                                <option value="normal" <?php echo (isset($settings['fonts']['menu']['type']) && $settings['fonts']['menu']['type'] == 'normal' ? ' selected' : '') ?>><?php echo Language::getVar('SUMO_NOUN_FONT_NORMAL')?></option>
                                <option value="italic" <?php echo (isset($settings['fonts']['menu']['type']) && $settings['fonts']['menu']['type'] == 'italic' ? ' selected' : '') ?>><?php echo Language::getVar('SUMO_NOUN_FONT_ITALIC')?></option>
                            </select>
                        </td>
                        <td>
                            <select name="fonts[menu][size]" class="form-control">
                                <?php for ($i = 12; $i <= 24; $i++) {
                                    echo '<option' . (isset($settings['fonts']['menu']['size']) && $settings['fonts']['menu']['size'] == $i ? ' selected' : '') . '>' . $i . 'px</option>';
                                }?>
                            </select>
                        </td>
                        <td>
                            <select name="fonts[menu][uppercase]" class="form-control">
                                <option value="0" <?php echo (isset($settings['fonts']['menu']['uppercase']) && !$settings['fonts']['menu']['uppercase'] ? ' selected' : '') ?>><?php echo Language::getVar('NO')?></option>
                                <option value="1" <?php echo (isset($settings['fonts']['menu']['uppercase']) && $settings['fonts']['menu']['uppercase'] == '1' ? ' selected' : '') ?>><?php echo Language::getVar('YES')?></option>
                            </select> 
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-4">
            <h4><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_FONTS_INFO')?></h4>
            <p><?php echo Language::getVar('SUMO_ADMIN_DESIGN_BUILDER_FONTS_CONTENT')?></p>
        </div>
    </div>
</form>
<script type="text/javascript">
$(function(){
    $('.loader').slideUp();
})
</script>