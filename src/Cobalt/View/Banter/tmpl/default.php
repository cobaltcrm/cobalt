<div class="infoContainer">
    <h2><?php echo ucwords(TextHelper::_('COBALT_BANTER_ROOMS')); ?></h2>
    <div class="container">
        <div class="filter_container">
            <?php echo TextHelper::_('COBALT_SHOW_TRANSCRIPTS_FOR'); ?>:
            <span class="filters" ><a class="dropdown" id="room_lists_link" ><?php if ( count($this->rooms) > 0 ) { echo $this->rooms[0]->name; } else { echo TextHelper::_('COBALT_NO_ROOMS'); }  ?></a></span>
            <div class="filters" id="room_lists">
                <ul>
                    <?php
                        if ( count($this->rooms) > 0 ) {
                            foreach ($this->rooms as $room) {
                                echo "<li><a class='filter_mailing_list_".$room->id." dropdown_item' onclick='updateTranscripts(".$room->id.")'>".$room->name."</a></li>";
                            }
                        }
                    ?>
                </ul>
            </div>
        </div>
    <div id="transcript_list">
        <table id="transcripts_table" class="com_cobalt_table">
            <thead>
                <tr>
                    <th><?php echo TextHelper::_('COBALT_ROOM'); ?></th>
                    <th><?php echo TextHelper::_('COBALT_DATE'); ?></th>
                    <th><?php echo TextHelper::_('COBALT_VIEW'); ?></th>
                </tr>
            </thead>
            <tbody id="transcript_entries">
                <?php
                     $transcript_list_view = ViewHelper::getView('banter','transcripts','phtml',array(array('ref'=>'transcripts','data'=>$this->transcripts)));
                     echo $transcript_list_view->render();
                ?>
            </tbody>
        </table>
    </div>
</div>
</div>
