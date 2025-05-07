<?php
if (!empty($record_obj->youtube)):
?>
<div class="video-responsive video-responsive-16-9">

    <iframe src="https://www.youtube-nocookie.com/embed/<?= $record_obj->youtube ?>" width="100%" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="">
        
        <span data-mce-type="bookmark" style="display: inline-block; width: 0px; overflow: hidden; line-height: 0;" class="mce_SELRES_start">﻿</span>

    </iframe>

</div>
<style>
.video-responsive{display:block;overflow:hidden;padding:0;position:relative;width:100%}.video-responsive::before{content:"";display:block;padding-bottom:56.25%}.video-responsive embed,.video-responsive iframe,.video-responsive object{border:0;bottom:0;height:100%;left:0;position:absolute;right:0;top:0;width:100%}video.video-responsive{height:auto;max-width:100%}video.video-responsive::before{content:none}.video-responsive-4-3::before{padding-bottom:75%}.video-responsive-1-1::before{padding-bottom:100%}
</style>
<?php endif; ?>