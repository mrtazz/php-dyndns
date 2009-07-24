<?php


function openDb($dbFile) {
  if ($db = sqlite_open($dbFile)) { 
    return $db;
  }

  return false;
}


function closeDb($db) {
  if (isset($_SESSION['db_alreadyOpen'])) {
    sqlite_close($db);
    unset($_SESSION['db_alreadyOpen']);
  }
}


function openDbIfNecessary($dbFile) {
  if (!isset($_SESSION['db_alreadyOpen'])) {
    $_SESSION['db_alreadyOpen'] = openDb($dbFile);
  }

  return $_SESSION['db_alreadyOpen'];
}







function insertInDb($db, $table, $array) {
  $fieldsPart = "(";
  $valuesPart = "(";

  foreach ($array as $key => $value) {
    $fieldsPart .= $key.", ";
    $valuesPart .= "\"".$value."\", ";
  }

  $fieldsPart = substr($fieldsPart, 0, -2);
  $fieldsPart .= ") ";
  $valuesPart = substr($valuesPart, 0, -2);
  $valuesPart .= ") ";

  $string = "INSERT INTO ".$table." ".$fieldsPart." VALUES ".$valuesPart;
  @$query = sqlite_query($string, $db);

  return sqlite_changes($db);
}







function updateInDb($db, $table, $array, $id) {
  $string = "UPDATE ".$table." SET ";

  foreach ($array as $key => $value) {
    $string .= $key." = \"".$value."\", ";
  }

  $string = substr($string, 0, -2);
  $string .= " WHERE id = '".$id."'";
  $query = sqlite_query($string, $db);

  return sqlite_changes($db);
}


function updateInDbByOF_ID($db, $table, $array, $of_id) {
  $string = "UPDATE ".$table." SET ";

  foreach ($array as $key => $value) {
    $string .= $key." = \"".$value."\", ";
  }

  $string = substr($string, 0, -2);
  $string .= " WHERE of_id = '".$of_id."'";
  $query = sqlite_query($string, $db);

  return sqlite_changes($db);
}






function tryUpdateElseInsert($db, $table, $array, $id = "") {
  $rowsAffected = @updateInDb($db, $table, $array, $id);

  if ($rowsAffected == 0) {
    $rowsAffected = insertInDb($db, $table, $array);
  }

  return $rowsAffected;
}


function tryUpdateElseInsertByOF_ID($db, $table, $array, $of_id = "") {
  $rowsAffected = @updateInDbByOF_ID($db, $table, $array, $of_id);

  if ($rowsAffected == 0) {
    $rowsAffected = insertInDb($db, $table, $array);
  }

  return $rowsAffected;
}


?>
