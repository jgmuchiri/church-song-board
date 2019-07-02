<div class="m-heading">
    Weekly Music Board
</div>

<div class="m-div">
    <div class="m-bg-image"></div>
    <div class="m-bg-text">

        <?php $curr = NULL; ?>
        <table class="m-table">
            <?php foreach ($songs as $key => $song): ?>
                <tr>
                    <td colspan="1">
                       <div class="m-date">
                           <?php
                           if(date('Y-m-d', strtotime($song->event_date)) !== $curr) {
                               $curr = date('Y-m-d', strtotime($song->event_date));
                               echo '<h4>'.date('d M, Y', strtotime($song->event_date)).'</h4>';
                           }
                           ?>
                       </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="m-title">
                            <?php echo $key + 1; ?>. <?php echo $song->title; ?>
                            <?php if(!empty($song->author)): ?>
                                <span class="m-author">by <?php echo $song->author; ?></span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="m-btns">
                            <?php if(!empty($song->video)): ?>
                                <a href="<?php echo $song->video; ?>" target="_blank" class="m-btn m-video-btn">
                                    <i class="fa fa-video-camera"></i>
                                    Video</a>
                            <?php endif; ?>

                            <?php if(!empty($song->lyrics)): ?>
                                <a href="<?php echo $song->lyrics; ?>" target="_blank" class="m-btn m-lyrics-btn">
                                    <i class="fa fa-music"></i>
                                    Lyrics</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

    </div>
</div>


