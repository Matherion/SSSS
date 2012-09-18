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

  // This is useful for debugging: if this is set, no email is sent.
  if (isset($_GET['debug'])) {
    echo('<form action="index.php?debug&action=submit" id="form" method="post" enctype="multipart/form-data">');
  }
  else {
    echo('<form action="index.php?action=submit" id="form" method="post" enctype="multipart/form-data">');
  }
?>
  <div class="formRow"><div class="formLabel">Naam:</div><input class="formInput" type="text" name="name" id="name" value="<?php echo($name); ?>"></input></div>
  <div class="formRow"><div class="formLabel">Student nummer:</div><input class="formInput" type="text" name="idnr" id="idnr" value="<?php echo($idnr); ?>"></input></div>
  <div class="formRow"><div class="formLabel">Email adres:</div><input class="formInput" type="text" name="email" id="email" value="<?php echo($email); ?>"></input></div>
  <div class="formBlock"><div class="formRow">
<?php
  foreach ($courses as $course) {
    echo("<div class=\"courseButton\" onclick='showBlock(\"{$course->id}\");'>{$course->name}</div>");
  }
?>
  </div><div class="clearFloat"></div></div>
  <div class="formBlock" style="display:none" id="begeleider">
    <div class="formRow"><div class="formLabel">Begeleider:</div><select class="formInput" name="begeleider">
      <option value="Nothing"></option><option value="No supervisor">Ik had geen begeleider</option>
<?php
  foreach ($teachers as $teacher) {
    echo("<option value=\"{$teacher->id}\">{$teacher->name}</option>");
  }
?>
    </select></div>
  </div>
<?php
  $showBlockJavaScriptFunction = "<script type=\"text/javascript\">showBlock = function (id) {";
  foreach ($courses as $course) {
    echo("<div class=\"formBlock\" style=\"display:none\" id=\"course_{$course->id}\">");
    include(FILES_PATH.$course->formfile);
    echo("</div>");
    $showBlockJavaScriptFunction .= 'if (id=="'.$course->id.'") { $("#course_'.$course->id.'").css("display","block");
  } else { $("#course_'.$course->id.'").css("display","none"); }';
 }
 $showBlockJavaScriptFunction .= '  $("#begeleider").css("display","block");
  $("input[name=course]").val(id);}
  </script>';

?>
  <div class="formRow"><div class="formLabel">Bericht:</div><textarea name="message" rows="4" cols="35"></textarea>
  <div class="formRow">
    <input type="hidden" name="course" value=""></input>
    <input type="submit" name="submit" value="Verstuur"></input>
  </div>
</form>