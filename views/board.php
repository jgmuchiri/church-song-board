<div class="m-div">
    <div class="m-bg-image"></div>
    <div class="m-bg-text">

        <?php
        $count = count($m_boards);
        for ($i = 0; $i < $count; $i++): ?>
        <?php
            $date= $m_boards[$i]->event_date;

            $songs = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.CMB_TABLE.' WHERE event_date LIKE \''.date('Y-m-d',strtotime($date)).'%\' ORDER BY sort_order, event_date DESC');
            echo '<h4>'.date('d M, Y',strtotime($date)).'</h4>'; ?>
        <table class="">
            <?php foreach ($songs as $key => $board): ?>
                <tr>
                    <td>
                        <div class="m-title">
                            <?php echo $key + 1; ?>. <?php echo $board->title; ?>
                            <?php if(!empty($board->author)): ?>
                                <span class="m-author">by <?php echo $board->author; ?></span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="m-btns">
                            <?php if(!empty($board->video)): ?>
                                <a href="<?php echo $board->video; ?>" target="_blank" class="m-btn m-video-btn">
                                    <i class="fa fa-video-camera"></i>
                                    Video</a>
                            <?php endif; ?>

                            <?php if(!empty($board->lyrics)): ?>
                                <a href="<?php echo $board->lyrics; ?>" target="_blank" class="m-btn m-lyrics-btn">
                                    <i class="fa fa-music"></i>
                                    Lyrics</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php endfor; ?>

    </div>
</div>


