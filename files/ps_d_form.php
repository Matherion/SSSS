<div class="subtitle">Inleveren eindopdracht Psychologisch Survey (taak D):</div>
<div class="headingBar">Student:</div>
<div class="formRow">
  <div class="formLabel">Naam:</div>
  <input class="formInput" type="text" name="name" id="ps_d_name" value="<?php echo($name); ?>"></input>
</div>
<div class="formRow">
  <div class="formLabel">Student nummer:</div>
  <input class="formInput" type="text" name="idnr" id="ps_d_idnr" value="<?php echo($idnr); ?>"></input>
</div>
<div class="formRow">
  <div class="formLabel">Email adres:</div>
  <input class="formInput" type="text" name="email" id="ps_d_email" value="<?php echo($email); ?>"></input>
</div>
<div class="headingBar">Begeleider:</div>
Zorg dat je de begeleider specificeert die je ABC huiswerkopdracht heeft nagekeken en die je heeft begeleid bij de eindopdracht (D).
  <div class="formRow"><div class="formLabel">Begeleider:</div>
    <select class="formInput" name="ps_d_supervisor">
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
      echo("      <option value=\"{$teacher->id}\">{$teacher->name}</option>\n");
    }
  }
?>
    </select>
  </div>
<div class="headingBar">Bestand:</div>
<div class="formRow"><div class="formLabel">Bestand:</div><input class="formInput" type="file" name="ps_d_file"></input></div>