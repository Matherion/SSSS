<?php
  $pe_student_1_visibility = ' style="display:block;" ';
  $pe_student_2_visibility = ' style="display:none;" ';
  $pe_nrOfPeople_1_checked = ' checked="checked ';
  $pe_nrOfPeople_2_checked = '  ';
  $pe_vew_2_visibility = 'style="display:none;" ';
  if ($_POST['pe_nrOfPeople'] == 2) {
    $pe_nrOfPeople_1_checked = '  ';
    $pe_nrOfPeople_2_checked = ' checked="checked ';
    $pe_student_2_visibility = ' style="display:block;" ';
    $pe_vew_2_visibility = 'style="display:block;" ';
  }
?>

<div class="subtitle">Inleveren tentamen Psychologisch Experiment</div>

<div class="headingBar">Samenwerking:</div>
<div class="formRow" >
  <input type="radio" name="pe_nrOfPeople" id="pe_nrOfPeople_1" value="1" <?php echo($pe_nrOfPeople_1_checked); ?> onclick="$('#pe_student_1').show();$('#pe_student_2').hide();$('#pe_vew_2').hide();"></input>
  <label for="pe_nrOfPeople_1" onclick="$('#pe_student_1').show();$('#pe_student_2').hide();$('#pe_vew_2').hide();">Ik heb dit verslag alleen geschreven</label>
</div>
<div class="formRow" >
  <input type="radio" name="pe_nrOfPeople" id="pe_nrOfPeople_2" value="2" <?php echo($pe_nrOfPeople_2_checked); ?>onclick="$('#pe_student_1').show();$('#pe_student_2').show();$('#pe_vew_2').show();"></input>
  <label for="pe_nrOfPeople_2" onclick="$('#pe_student_1').show();$('#pe_student_2').show();$('#pe_vew_2').show();">Wij hebben samengewerkt</label>
</div>
<div class="formSection" <?php echo($pe_student_1_visibility); ?> id="pe_student_1">
  <div class="headingBar">Student 1:</div>
  <div class="formRow">
    <div class="formLabel">Naam:</div>
    <input class="formInput" type="text" name="pe_name_1" id="name" value="<?php echo($_POST['pe_name_1']); ?>"></input>
  </div>
  <div class="formRow">
    <div class="formLabel">Student nummer:</div>
    <input class="formInput" type="text" name="pe_idnr_1" id="idnr" value="<?php echo($_POST['pe_idnr_1']); ?>"></input>
  </div>
  <div class="formRow">
    <div class="formLabel">Email adres:</div>
    <input class="formInput" type="text" name="pe_email_1" id="email" value="<?php echo($_POST['pe_email_1']); ?>"></input>
  </div>
</div>
<div class="formSection" <?php echo($pe_student_2_visibility); ?> id="pe_student_2">
  <div class="headingBar">Student 2:</div>
  <div class="formRow">
    <div class="formLabel">Naam:</div>
    <input class="formInput" type="text" name="pe_name_2" id="name" value="<?php echo($_POST['pe_name_2']); ?>"></input>
  </div>
  <div class="formRow">
    <div class="formLabel">Student nummer:</div>
    <input class="formInput" type="text" name="pe_idnr_2" id="idnr" value="<?php echo($_POST['pe_idnr_2']); ?>"></input>
  </div>
  <div class="formRow">
    <div class="formLabel">Email adres:</div>
    <input class="formInput" type="text" name="pe_email_2" id="email" value="<?php echo($_POST['pe_email_2']); ?>"></input>
  </div>
</div>

<div class="headingBar">Begeleider:</div>
Selecteer de begeleider bij wie je praktijksessies hebt gevolgd - deze persoon zal je verslag nakijken. Als je geen sessies hebt gevolgd, wordt er automatisch een begeleider toegekend - in dat geval moet je de optie "Ik had geen begeleider" selecteren.<br />
<div class="formBlock">
  <div class="formRow"><div class="formLabel">Begeleider:</div>
    <select class="formInput" name="pe_supervisor">
      <option value="Nothing"></option>
      <option value="No supervisor">Ik had geen begeleider</option>
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
      echo("      <option value=\"{$teacher->id}\" ".($teacher->id == $_POST['pe_supervisor'] ?"selected":"").">{$teacher->name}</option>\n");
    }
  }
?>
    </select>
  </div>
</div>
<div class="headingBar">Bestanden:</div>
<div class="formRow"><div class="formLabel">Verslag:</div><input class="formInput" type="file" name="pe_paper"></input></div>
<div class="formRow"><div class="formLabel">Data bestand:</div><input class="formInput" type="file" name="pe_data"></input></div>
<div class="formRow"><div class="formLabel">Verklaring eigen werk 1:</div><input class="formInput" type="file" name="pe_ownwork_1"></input></div>
<div class="formRow" <?php echo($pe_vew_2_visibility); ?> id="pe_vew_2" ><div class="formLabel">Verklaring eigen werk 2:</div><input class="formInput" type="file" name="pe_ownwork_2"></input></div>
<div class="hintBlock">
  <strong>Tips:</strong>
  <ul>
    <li>Zorg dat je je verslag in &eacute;&eacute;n bestand hebt staan. Dit bestand moet in een van de volgende formaten zijn: portable document format (.pdf), open document format (.odf), rich text file (.rtf), MS Word document (.doc of .docx).</li>
    <li>Je verklaring eigen werk moet in een van de volgende formaten zijn: portable document format (.pdf), .jpeg of .jpg (staat voor "Joint Photographic Experts Group"), of tagged image file format (.tiff of .tif).</li>
    <li>Je verklaring eigen werk moet dus digitaal worden ingeleverd! Je kunt op verschillende manieren aan een digitale ingevulde versie van je verklaring eigen werk komen:
      <ul>
        <li>
          Print, teken, en scan het formulier met een scanner;
        </li>
        <li>
          Geen scanner? Gebruik je telefoon! CamScanner is een gratis app die dit kan. Zie <a href="https://play.google.com/store/apps/details?id=com.intsig.camscanner">Google Play voor Android</a> en <a href="https://itunes.apple.com/app/id388627783">iTunes voor iOS</a>.
        </li>
        <li>
          Geen smartphone? Gebruik een camera! Er is een gratis programma dat een acceptabele scan kan maken van een digitale foto, <a href="http://corey.elsewhere.org/camscan/">zie hier</a>.
        </li>
        <li>
          Scan een handtekening van jezelf in, en vul het formulier digitaal in en plaats de handtekening digitaal. Dit kan o.a. met <a href="http://pdf-xchange-viewer.en.softonic.com/">het gratis programma PDF-XChange viewer</a>; je kunt een document omzetten naar PDF met <a href="http://www.pdfforge.org/pdfcreator">het gratis (open source) programma PDFCreator</a>.
        </li>
      </ul>
    </li>
    <li>Gebruik eventueel de gratis online service <a href="http://zamzar.com">ZamZar</a> om je bestanden te converteren naar het juiste formaat.</li>
  </ul>
</div>
