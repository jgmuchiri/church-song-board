<div class="wrap">
    <h1 class="wp-heading-inline">Church Music Board</h1>
    <p>Complete the form below</p>
    <?php if($done): ?>
    <div class="notice notice-success is-dismissible">
        <p>Song has been <?php echo empty($song)?'added!':'updated!'; ?></p>
    </div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="<?php echo empty($song)?'newsong':'updatesong'; ?>" value="1"/>
        <table class="table">
            <tr>
                <td>Date/Time:</td>
                <td>
                    <input type="date" value="<?php echo empty($song)?date('Y-m-d',strtotime('next sunday')): date('Y-m-d',strtotime($song->event_date)); ?>" name="event_date" class="regular-text" required placeholder="Date"/>
                    <br/>
                    <input type="time" name="event_date_t" value="<?php echo empty($song)?'10:00': date('H:i',strtotime($song->event_date)); ?>" class="regular-text" required placeholder="Time"/>
                </td>
            </tr>
            <tr>
                <td>Song title:</td>
                <td><input type="text" name="title" value="<?php echo !empty($song)?$song->title:'';?>" class="regular-text" required placeholder="Enter song title"/></td>
            </tr>
            <tr>
                <td> Song author:</td>
                <td><input type="text" name="author" value="<?php echo !empty($song)?$song->author:'';?>"  class="regular-text" placeholder="Enter song author"/></td>
            </tr>
            <tr>
                <td>Video URL:</td>
                <td><input type="text" name="video" value="<?php echo !empty($song)?$song->video:'';?>" class="regular-text" placeholder="Paste video URL"/></td>
            </tr>
            <tr>
                <td>Lyrics URL:</td>
                <td><input type="text" name="lyrics"  value="<?php echo !empty($song)?$song->lyrics:'';?>" class="regular-text" placeholder="Paste Lyrics URL"/></td>
            </tr>
            <tr>
                <td>Sort Order:</td>
                <td><input type="number" name="sort_order"  value="<?php echo !empty($song)?$song->sort_order:'';?>" placeholder="1 is top"/></td>
            </tr>
            <tr>
                <td></td>
                <td class="text-right"><button class="button action">Save</button></td>
            </tr>
        </table>

    </form>
</div>