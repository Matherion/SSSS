<?php
  $ls_bcd_student_1_visibility = ' style="display:block;" ';
  $ls_bcd_student_2_visibility = ' style="display:block;" ';
  $ls_bcd_nrOfPeople_1_checked = '  ';
  $ls_bcd_nrOfPeople_2_checked = ' checked="checked ';
  $ls_bcd_vew_2_visibility = 'style="display:block;" ';
  if ($_POST['ls_bcd_nrOfPeople'] == 1) {
    $ls_bcd_nrOfPeople_1_checked = ' checked="checked ';
    $ls_bcd_nrOfPeople_2_checked = '  ';
    $ls_bcd_student_2_visibility = ' style="display:none;" ';
    $ls_bcd_vew_2_visibility = 'style="display:none;" ';
  }
?>

<div class="subtitle">Inleveren tentamen Literatuurstudie (taken B-D)</div>

<div class="headingBar">Samenwerking:</div>
Geef hieronder aan of bij literatuurstudie is samengewerkt of dat zelfstandig is gewerkt (dit moet natuurlijk hetzelfde zijn als wat bij het inleveren van A is aangegeven).

<div class="formRow" >
  <input type="radio" name="ls_bcd_nrOfPeople" id="ls_bcd_nrOfPeople_2" value="2" <?php echo($ls_bcd_nrOfPeople_2_checked); ?>onclick="$('#ls_bcd_student_1').show();$('#ls_bcd_student_2').show();$('#ls_bcd_vew_2').show();"></input>
  <label for="ls_bcd_nrOfPeople_2" onclick="$('#ls_bcd_student_1').show();$('#ls_bcd_student_2').show();$('#ls_bcd_vew_2').show();">Wij werken samen</label>
</div>
<div class="formRow" >
  <input type="radio" name="ls_bcd_nrOfPeople" id="ls_bcd_nrOfPeople_1" value="1" <?php echo($ls_bcd_nrOfPeople_1_checked); ?> onclick="$('#ls_bcd_student_1').show();$('#ls_bcd_student_2').hide();$('#ls_bcd_vew_2').hide();"></input>
  <label for="ls_bcd_nrOfPeople_1" onclick="$('#ls_bcd_student_1').show();$('#ls_bcd_student_2').hide();$('#ls_bcd_vew_2').hide();">Ik heb toestemming gekregen om zelfstandig te werken</label>
</div>

<div class="formSection" <?php echo($ls_bcd_student_1_visibility); ?> id="ls_bcd_student_1">
  <div class="headingBar">Student 1:</div>
  <div class="formRow">
    <div class="formLabel">Naam:</div>
    <input class="formInput" type="text" name="ls_bcd_name_1" id="name" value="<?php echo($_POST['ls_bcd_name_1']); ?>"></input>
  </div>
  <div class="formRow">
    <div class="formLabel">Student nummer:</div>
    <input class="formInput" type="text" name="ls_bcd_idnr_1" id="idnr" value="<?php echo($_POST['ls_bcd_idnr_1']); ?>"></input>
  </div>
  <div class="formRow">
    <div class="formLabel">Email adres:</div>
    <input class="formInput" type="text" name="ls_bcd_email_1" id="email" value="<?php echo($_POST['ls_bcd_email_1']); ?>"></input>
  </div>
</div>

<div class="formSection" <?php echo($ls_bcd_student_2_visibility); ?> id="ls_bcd_student_2">
  <div class="headingBar">Student 2:</div>
  <div class="formRow">
    <div class="formLabel">Naam:</div>
    <input class="formInput" type="text" name="ls_bcd_name_2" id="name" value="<?php echo($_POST['ls_bcd_name_2']); ?>"></input>
  </div>
  <div class="formRow">
    <div class="formLabel">Student nummer:</div>
    <input class="formInput" type="text" name="ls_bcd_idnr_2" id="idnr" value="<?php echo($_POST['ls_bcd_idnr_2']); ?>"></input>
  </div>
  <div class="formRow">
    <div class="formLabel">Email adres:</div>
    <input class="formInput" type="text" name="ls_bcd_email_2" id="email" value="<?php echo($_POST['ls_bcd_email_2']); ?>"></input>
  </div>
</div>

<div class="headingBar">Begeleider:</div>
Selecteer de begeleider die uw taak A heeft nagekeken, en die feedback gaf op de eerdere versies van B-D. Deze persoon zal deze eindversie van B-D ook nakijken.
<div class="formBlock">
  <div class="formRow"><div class="formLabel">Begeleider:</div>
    <select class="formInput" name="ls_bcd_supervisor">
      <option value="Nothing"></option>
      <!--option value="No supervisor">Ik had geen begeleider</option-->
<?php
  // This file is included by view_form.php, and at this moment,
  // $currentCourse contains the object of the current course.
  $course_id = $currentCourse->id; // Bound to the `course` MySQL column using PDO
  foreach ($teachers as $teacher) {
    $teacher_id = $teacher->id; // Bound to the `teacher` MySQL column using PDO
    // Get this teacher's capacity for this course
    if (!$getCapacity->execute()) {
      errorHandler("Errorcode: {$sth->errorCode()}, errorinfo: {$sth->errorInfo ()}.");
    }
    $tempCapacityArray = $getCapacity->fetch();
    if ($tempCapacityArray['capacity'] > 0) {
      echo("      <option value=\"{$teacher->id}\" ".($teacher->id == $_POST['ls_bcd_supervisor'] ?"selected":"").">{$teacher->name}</option>\n");
    }
  }
?>
    </select>
  </div>
</div>

<div class="headingBar">Bestanden:</div>
<div class="formRow"><div class="formLabel">B:</div><input class="formInput" type="file" name="ls_bcd_b"></input></div>
<div class="formRow"><div class="formLabel">C:</div><input class="formInput" type="file" name="ls_bcd_c"></input></div>
<div class="formRow"><div class="formLabel">D:</div><input class="formInput" type="file" name="ls_bcd_d"></input></div>
<div class="formRow"><div class="formLabel">Verklaring eigen werk 1:</div><input class="formInput" type="file" name="ls_bcd_ownwork_1"></input></div>
<div class="formRow" <?php echo($ls_bcd_vew_2_visibility); ?> id="ls_bcd_vew_2" ><div class="formLabel">Verklaring eigen werk 2:</div><input class="formInput" type="file" name="ls_bcd_ownwork_2"></input></div>

<div class="hintBlock">
  <strong>Tips:</strong>
  <ul>
    <li>Zorg dat u de volledige B, C, en D elk in &eacute;&eacute;n bestand heeft staan. De meeste docenten kunnen alleen Microsoft Word bestanden openen (.doc of .docx). In overleg met uw begeleider kunt u eventueel ook een bestand insturen in een van de volgende formaten: portable document format (.pdf), open document format (.odf), of rich text file (.rtf).</li>
    <li>De verklaring eigen werk moet in een van de volgende formaten zijn: portable document format (.pdf), .jpeg of .jpg (staat voor "Joint Photographic Experts Group"), tagged image file format (.tiff of .tif), of .doc of .docx (Microsoft Word document).</li>
    <li>De verklaring eigen werk moet dus digitaal worden ingeleverd! U kunt op verschillende manieren aan een digitale ingevulde versie van de verklaring eigen werk komen:
      <ul>
        <li>
          Print, teken, en scan het formulier met een scanner;
        </li>
        <li>
          Geen scanner? Gebruik een telefoon! CamScanner is een gratis app die dit kan (er zijn er vast meer). Zie <a href="https://play.google.com/store/apps/details?id=com.intsig.camscanner">Google Play voor Android</a> en <a href="https://itunes.apple.com/app/id388627783">iTunes voor iOS</a>.
        </li>
        <li>
          Geen smartphone? Gebruik een camera! Er is een gratis programma dat een acceptabele scan kan maken van een digitale foto, <a href="http://corey.elsewhere.org/camscan/">zie hier</a>.
        </li>
        <li>
          Scan een handtekening in, en vul het formulier digitaal in en plaats de handtekening digitaal. Dit kan o.a. met <a href="http://pdf-xchange-viewer.en.softonic.com/">het gratis programma PDF-XChange viewer</a>; een document kan worden omgezet naar PDF met <a href="http://www.pdfforge.org/pdfcreator">het gratis (open source) programma PDFCreator</a>.
        </li>
      </ul>
    </li>
    <li>Gebruik eventueel de gratis online service <a href="http://zamzar.com">ZamZar</a> om bestanden te converteren naar het juiste formaat.</li>
  </ul>
</div>
