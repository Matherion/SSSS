<div class="subtitle">Inleveren huiswerkopdracht Psychologisch Survey (taken A, B en C)</div>

<div class="headingBar">Samenwerking:</div>
Voor de ABC opdracht van Psychologisch Survey krijgt elke student een eigen datafile, en elke student moet dus een individueel (uniek) verslag inleveren. Hoewel je natuurlijk mag samenwerken, levert elke student dus een eigen ABC opdracht in, dus elke student moet dit formulier apart invullen voor zijn/haar eigen verslag. In het bericht-veld (onderaan) kan worden aangegeven dat is samengewerkt. Beide studenten moeten dan dezelfde begeleider selecteren! Als geen sessies zijn gevolgd, wordt een docent toegekend als de eerste student zijn/haar verslag indient. In dat geval moet de student die de opdracht als tweede inlevert, dezelfde docent selecteren die aan de eerste student is toegekend.
<div class="headingBar">Student:</div>
<div class="formRow">
  <div class="formLabel">Naam:</div>
  <input class="formInput" type="text" name="ps_abc_name" id="name" value="<?php echo($name); ?>"></input>
</div>
<div class="formRow">
  <div class="formLabel">Student nummer:</div>
  <input class="formInput" type="text" name="ps_abc_idnr" id="idnr" value="<?php echo($idnr); ?>"></input>
</div>
<div class="formRow">
  <div class="formLabel">Email adres:</div>
  <input class="formInput" type="text" name="ps_abc_email" id="email" value="<?php echo($email); ?>"></input>
</div>

<div class="headingBar">Begeleider:</div>
Selecteer de begeleider bij wie je praktijksessies hebt gevolgd. Als je geen sessies hebt gevolgd, wordt er automatisch een begeleider toegekend - in dat geval moet je de optie "Ik had geen begeleider" selecteren. De begeleider die je ABC huiswerkopdracht nakijkt, zal je ook begeleiden bij je eindopdracht (D).
<div class="formBlock">
  <div class="formRow"><div class="formLabel">Begeleider:</div>
    <select class="formInput" name="ps_abc_supervisor">
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
      echo("      <option value=\"{$teacher->id}\" ".($teacher->id == $_POST['ps_abc_supervisor'] ?"selected":"").">{$teacher->name}</option>\n");
    }
  }
?>
    </select>
  </div>
</div>

<div class="headingBar">Bestanden en nummer datafile:</div>
<div class="formRow"><div class="formLabel">Nummer datafile:</div><input class="formInput" type="text" name="ps_abc_datanr" value="<?php echo($ps_abc_datanr); ?>"></input></div>
<div class="formRow"><div class="formLabel">ABC huiswerkopdracht:</div><input class="formInput" type="file" name="ps_abc_file"></input></div>
<div class="hintBlock">
  <strong>Tips:</strong>
  <ul>
    <li>Zorg dat je je ABC opdracht in &eacute;&eacute;n bestand hebt staan. Dit bestand moet in een van de volgende formaten zijn: portable document format (.pdf), open document format (.odf), rich text file (.rtf), MS Word document (.doc of .docx).</li>
    <li>Gebruik eventueel de gratis online service <a href="http://zamzar.com">ZamZar</a> om je bestanden te converteren naar het juiste formaat.</li>
  </ul>
</div>
