<?php
  $ls_a_student_1_visibility = ' style="display:block;" ';
  $ls_a_student_2_visibility = ' style="display:block;" ';
  $ls_a_nrOfPeople_1_checked = '  ';
  $ls_a_nrOfPeople_2_checked = ' checked="checked" ';
  if ($_POST['ls_a_nrOfPeople'] == 1) {
    $ls_a_nrOfPeople_1_checked = ' checked="checked ';
    $ls_a_nrOfPeople_2_checked = '  ';
    $ls_a_student_1_visibility = ' style="display:none;" ';
  }
?>

<div class="subtitle">Inleveren taak A voor Literatuurstudie</div>

<div class="headingBar">Samenwerking:</div>
Voor Literatuur Studie moet in principe met twee studenten worden samengewerkt. In sommige situaties kan van de examinator toestemming worden verkregen om het zelfstandig te doen. Geef dat dan hieronder aan.
<div class="formRow" >
  <input type="radio" name="ls_a_nrOfPeople" id="ls_a_nrOfPeople_2" value="2" <?php echo($ls_a_nrOfPeople_2_checked); ?>onclick="$('#ls_a_student_1').show();$('#ls_a_student_2').show();$('#ls_a_vew_2').show();"></input>
  <label for="ls_a_nrOfPeople_2" onclick="$('#ls_a_student_1').show();$('#ls_a_student_2').show();$('#ls_a_vew_2').show();">Wij werken samen</label>
</div>
<div class="formRow" >
  <input type="radio" name="ls_a_nrOfPeople" id="ls_a_nrOfPeople_1" value="1" <?php echo($ls_a_nrOfPeople_1_checked); ?> onclick="$('#ls_a_student_1').show();$('#ls_a_student_2').hide();$('#ls_a_vew_2').hide();"></input>
  <label for="ls_a_nrOfPeople_1" onclick="$('#ls_a_student_1').show();$('#ls_a_student_2').hide();$('#ls_a_vew_2').hide();">Ik heb toestemming gekregen om zelfstandig te werken</label>
</div>

<div class="formSection" <?php echo($ls_a_student_1_visibility); ?> id="ls_a_student_1">
  <div class="headingBar">Student 1:</div>
  <div class="formRow">
    <div class="formLabel">Naam:</div>
    <input class="formInput" type="text" name="ls_a_name_1" id="name" value="<?php echo($_POST['ls_a_name_1']); ?>"></input>
  </div>
  <div class="formRow">
    <div class="formLabel">Student nummer:</div>
    <input class="formInput" type="text" name="ls_a_idnr_1" id="idnr" value="<?php echo($_POST['ls_a_idnr_1']); ?>"></input>
  </div>
  <div class="formRow">
    <div class="formLabel">Email adres:</div>
    <input class="formInput" type="text" name="ls_a_email_1" id="email" value="<?php echo($_POST['ls_a_email_1']); ?>"></input>
  </div>
</div>

<div class="formSection" <?php echo($ls_a_student_2_visibility); ?> id="ls_a_student_2">
  <div class="headingBar">Student 2:</div>
  <div class="formRow">
    <div class="formLabel">Naam:</div>
    <input class="formInput" type="text" name="ls_a_name_2" id="name" value="<?php echo($_POST['ls_a_name_2']); ?>"></input>
  </div>
  <div class="formRow">
    <div class="formLabel">Student nummer:</div>
    <input class="formInput" type="text" name="ls_a_idnr_2" id="idnr" value="<?php echo($_POST['ls_a_idnr_2']); ?>"></input>
  </div>
  <div class="formRow">
    <div class="formLabel">Email adres:</div>
    <input class="formInput" type="text" name="ls_a_email_2" id="email" value="<?php echo($_POST['ls_a_email_2']); ?>"></input>
  </div>
</div>

<div class="headingBar">Begeleider:</div>
Uw begeleider wordt automatisch toegekend. Als dit formulier succesvol is ingeleverd, ziet u welke begeleider is toegekend. Bovendien ontvangt u een cc van het mailtje waarmee uw opdracht A aan de begeleider wordt verstuurd; daardoor heeft u gelijk het e-mail adres van uw begeleider. Deze begeleider begeleidt u tot en met opdracht BCD van Literatuur Studie.
<!--
  U kunt hieronder opgeven of u een voorkeur heeft voor een begeleider. Hieronder staat welke begeleiders actief zijn in de verschillende studiecentra.
  <ul>
  <li>Dirk Hoek: Utrecht en Den Haag</li>
  <li>Gjalt-Jorn Peters: Belgi&euml; (Antwerpen, Brussel, Gent, Hasselt, Kortrijk en Leuven)</li>
  <li>Hein Lodewijkx: Vlissingen en Breda</li>
  <li>Lukas Mouton: Utrecht</li>
  <li>Matthee Reijnders: Amsterdam en Alkmaar</li>
  <li>Natascha de Hoog: Eindhoven en Parkstad</li>
  <li>Peter Verboon: Rotterdam</li>
  <li>Rolf van Geel: Nijmegen en Zwolle</li>
  <li>Steffie van der Steen: Groningen, Leeuwarden, en Emmen</li>
  </ul>
  <div class="formBlock">
    <div class="formRow"><div class="formLabel">Begeleider:</div>
      <select class="formInput" name="ls_a_supervisor">
        <option value="Nothing"></option>
        <option value="No supervisor">Geen voorkeur</option>
<?php
  // This file is included by view_form.php, and at this moment,
  // $currentCourse contains the object of the current course.
/*
  $course_id = $currentCourse->id; // Bound to the `course` MySQL column using PDO
  foreach ($teachers as $teacher) {
    $teacher_id = $teacher->id; // Bound to the `teacher` MySQL column using PDO
    // Get this teacher's capacity for this course
    if (!$getCapacity->execute()) {
      errorHandler("Errorcode: {$sth->errorCode()}, errorinfo: {$sth->errorInfo ()}.");
    }
    $tempCapacityArray = $getCapacity->fetch();
    if ($tempCapacityArray['capacity'] > 0) {
      echo("      <option value=\"{$teacher->id}\" ".($teacher->id == $_POST['ls_a_supervisor'] ?"selected":"").">{$teacher->name}</option>\n");
    }
  }
*/
?>
    </select>
  </div>
</div>
-->

<div class="headingBar">Bestand:</div>
<div class="formRow">
  <div class="formLabel">Bestand:</div>
  <input class="formInput" type="file" name="ls_a_a"></input>
</div>
<div class="hintBlock">
  <strong>Tips:</strong>
  <ul>
    <li>Zorg dat u de gehele taak A in &eacute;&eacute;n bestand heeft staan. De meeste docenten kunnen alleen Microsoft Word bestanden openen en becommentari&euml;ren (.doc of .docx); voor sommige docenten zijn de volgende formaten ook in orde: portable document format (.pdf), open document format (.odf), of rich text file (.rtf). Als u geen Microsoft Word heeft, en niet in .doc of .docx kunt saven (soms lukt dit niet vanuit LibreOffice of OpenOffice), kunt u dit document in een ander formaat inleveren. Als uw docent dit niet goed kan openen, dan zal hij of zij contact met u opnemen.</li>
    <li>Gebruik eventueel de gratis online service <a href="http://zamzar.com">ZamZar</a> om het bestand te converteren naar het juiste formaat.</li>
  </ul>
</div>
