<?php
/***************************************************

 Smooth Student Submission System (SSSS)

 This file is part of SSSS, which is licensed
 under the Creative Commons: Attribution,
 Non-Commercial, Share Alike license (see
 http://creativecommons.org/licenses/by-nc-sa/3.0/)
 
 The first version was developed by
 Gjalt-Jorn Peters for the Dutch Open University
 in September 2012.
 
***************************************************/

  // The debug GET variable is useful for debugging: if this is set, no email is sent.
  // The admin GET variable enabled normal use of the site when it's in maintenance (see
  // index.php).
  echo('<form action="index.php?'.(isset($_GET['debug'])?"debug&":"").(isset($_GET['admin'])?"admin&":"").'action=submit" id="form" method="post" enctype="multipart/form-data" accept-charset="ISO-8859-1">');
?>
    <div class="headingBar">Cursus en/of onderdeel:</div>
      <div class="formBlock">
        <div class="formRow">
<?php
  foreach ($courses as $currentCourse) {
    echo("<div class=\"courseButton\" onclick='showBlock(\"{$currentCourse->id}\");'>{$currentCourse->name}</div>");
  }
?>
        </div>
        <div class="clearFloat"></div>
      </div>
<?php
  $showBlockJavaScriptFunction = "<script type=\"text/javascript\">showBlock = function (id) {";
  foreach ($courses as $currentCourse) {
    // If this is a resubmission, one of the courses is already
    // selected. If that is the case, show it; all other courses
    // are initially hidden.
    if ($course == $currentCourse->id) {
      echo("<div class=\"formBlock\" style=\"display:block\" id=\"course_{$currentCourse->id}\">");
    }
    else {
      echo("<div class=\"formBlock\" style=\"display:none\" id=\"course_{$currentCourse->id}\">");
    }
    include(FILES_PATH.$currentCourse->formfile);
    echo("</div>");
    $showBlockJavaScriptFunction .= 'if (id=="'.$currentCourse->id.'") { $("#course_'.$currentCourse->id.'").css("display","block");
  } else { $("#course_'.$currentCourse->id.'").css("display","none"); }';
  }
  $showBlockJavaScriptFunction .= '  $("#formEnd").css("display","block");
  $("input[name=course]").val(id);}
  </script>';

?>

    <div id="formEnd" <?php echo(($course>0?'style="display:block;"':'style="display:none;"')); ?> >
      <div class="headingBar">Bericht (optioneel):</div>
      <div class="formRow">
        <div class="formLabel">Bericht:</div>
        <textarea name="message" rows="4" cols="35"><?php echo($_POST['message']);?></textarea>
      </div>
      <br />
      <div class="headingBar">Inleveren:</div>
      <br />Controleer de email adres(sen), en zorg dat deze klopt (of kloppen) alvorens dit formulier in te leveren!
      <div class="formRow">
        <input type="hidden" name="course" value="<?php echo($course); ?>"></input>
        <input type="submit" name="submit" value="Verstuur"></input>
      </div>
    </div>
  </form>
  
<?php

  echo($showBlockJavaScriptFunction);

?>