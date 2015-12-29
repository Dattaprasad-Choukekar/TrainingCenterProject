<?php
require_once("DemoDB.php");

/** Access to the quizz table.
 * Put here the methods like getBySomeCriteriaSEarch,
 * insert(someQuizzArray), update(someQuizzArray) */
class PersonModel {

  /** Get quizz data for id $quizzId
   * (here demo with a SQL request about an existing table)
   * @param int $quizzId id of the quizz to be retrieved
   * @return associative_array table row
   */
  public static function get($quizzId) {
    $db = DemoDB::getConnection();
    $sql = "SELECT person_id, name
              FROM person
              WHERE person_id = :quizz_id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(":quizz_id", $quizzId);
    $ok = $stmt->execute();
    if ($ok) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }
  }

}

?>