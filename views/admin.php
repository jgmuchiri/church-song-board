<div class="wrap">
    <h1 class="wp-heading-inline">Church Music Board</h1>

    Shortcode
    <code>[mboard]</code>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <form method="post">
                        <?php
                        $this->songs_obj->prepare_items();
                        $this->songs_obj->display(); ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>