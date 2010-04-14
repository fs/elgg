<?php
/**
* iZAP izap_videos
*
* @package Elgg videotizer, by iZAP Web Solutions.
* @license GNU Public License version 3
* @author iZAP Team "<support@izap.in>"
* @link http://www.izap.in/
* @version 1.6.1-3.0
*/

global $IZAPSETTINGS;

// tabs array
$options = array(
  'settings',
  'queue',
  'severAnalysis'
);

$selectedTab = get_input('option');
if(empty($selectedTab)){
  $selectedTab = 'settings';
}

$form = izapCreateForm_izap_videos($formArray);
?>
<div class="ContentWrapper">
    <div id="elgg_horizontal_tabbed_nav">
    <ul>
    <?php
    foreach ($options as $option) {
      ?>
      <li class="<?php echo ($option == $selectedTab) ? 'selected' : '';?>">
        <a href="?option=<?php echo $option?>">
          <?php echo elgg_echo('video:' . $option);?>
        </a>
      </li>
      <?php
    }
    ?>
    </ul>
    </div>


  <?php
  if($selectedTab == 'settings'){
  ?>
  <form action="" method="post" enctype="multipart/form-data">
<p><label>Path for the PHP interpreter <br />
<input type="text"   name=""  value="C:/xampp/php/php" class="input-text"/></label></p>
<p><label>Video converting command <br />
<input type="text"   name=""  value="C:/xampp/htdocs/elggcampus/mod/video/ffmpeg/bin/ffmpeg.exe -y -i [inputVideoPath] -vcodec libx264 -vpre C:/xampp/htdocs/elggcampus/mod/video/ffmpeg/presets/libx264-hq.ffpreset -b 300k -bt 300k -ar 22050 -ab 48k -s 400x400 [outputVideoPath]" class="input-text"/></label></p>
<p><label>Video thumbnail command <br />
<input type="text"   name=""  value="C:/xampp/htdocs/elggcampus/mod/video/ffmpeg/bin/ffmpeg.exe -y -i [inputVideoPath] -vframes 1 -ss 00:00:10 -an -vcodec png -f rawvideo -s 320x240 [outputImage]" class="input-text"/></label></p>
<p><label>Enter max file size (in Mb.) <br />
<input type="text"   name=""  value="20" class="input-text"/></label></p>

<input onfocus="blur()" name=""  type="submit" class="submit_button" id="butnSubmit_"izapForm value="Save settings"  /></form>

<?php }else{
  echo "something in here for the relevent tab.";
}
?>
</div>