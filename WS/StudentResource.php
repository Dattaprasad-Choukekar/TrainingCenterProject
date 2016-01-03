<?php
/** Resource for a person. URL: person-{personId}.
 * idArtiste contains only digits: regexp [0-9]+
 * Methods:
 * <ul>
 *  <li>GET to retrieve. Possible responses:
 *    <ul>
 *      <li>200 json representation {person_id:..., name:...}</li>
 *      <li>400 idNotPositiveInteger</li>
 *      <li>404</li>
 *    </ul>
 *  </li>
 *  <li>PUT to update, with name parameter. Reponses:
 *    <ul>
 *      <li>204 Ok no content</li>
 *      <li>400 idNotPositiveInteger or nameMandatoryAndNotEmpty</li>
 *      <li>401 authorized only to admin/admin</li>
 *      <li>404</li>
 *      <li>409 duplicateName</li>
 *    </ul>
 *  </li>
 *  <li>DELETE to delete the person. Responses:
 *    <ul>
 *      <li>204 Ok no content</li>
 *      <li>400 idNotPositiveInteger or nameMandatoryAndNotEmpty</li>
 *      <li>401 authorized only to admin/admin</li>
 *      <li>404</li>
 *    </ul>
 *  </li>
 * </ul>
 *
 */
require_once ("HttpResource.php");
require_once ("DemoDB.php");

class StudentResource extends HttpResource
{
    /** Person id */
    protected $id;

    /** Initialize $id. Send 400 if id missing or not positive integer */
    public function init()
    {
        if (isset($_GET["id"])) {
            if (is_numeric($_GET["id"])) {
                $this->id = 0 + $_GET["id"]; // transformer en numerique
                if (!is_int($this->id) || $this->id <= 0) {
                    $this->exit_error(400, "idNotPositiveInteger");
                }
            } else {
                if ($_GET["id"] != "*") {
                    $this->exit_error(400, "idNotPositiveInteger");

                } else {
                    $this->id = 0;

                }
            }
        } else {
            $this->exit_error(400, "idRequis");
        }
    }

    protected function do_get()
    {
        // Call the parent
        parent::do_get();
        if ($_GET["id"] == "*") {
            if (isset($_GET["owner_id"])) {
                if (is_numeric($_GET["owner_id"])) {
                  //  $this->getAllProjectsByOwnerIdOrClassId(null);
                    return;
                } else {
                    $this->exit_error(400, "owner_idNotPositiveInteger");
                }
            }
            if (isset($_GET["class_id"])) {
                if (is_numeric($_GET["class_id"])) {
                    $this->getStudentsByClassId($_GET["class_id"]);
                    return;
                } else {
                    $this->exit_error(400, "class_idNotPositiveInteger");
                }
            }

            $this->getAllStudents();
        } else {
            
             $this->getStudentById();

        }
    }

    function getAllStudents()
    {
        try {

            $db = DemoDB::getConnection();
            $sql = "SELECT * FROM Student";
            $stmt = $db->prepare($sql);
            $ok = $stmt->execute();
            $sbody = "{";
            if ($ok) {
                if (isset($this->statusCode)) {
                    $this->statusCode = 200;
                }
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($row != null) {
                        if (isset($this->headers)) {
                            $this->headers[] = "Content-type: text/json; charset=utf-8";
                        }
                        // Produce utf8 encoded json
                        $sbody .= "\"" . $row["student_id"] . "\":" . json_encode($row) . ",";
                    } else {
                        $this->exit_error(404);
                    }
                }
                $sbody = rtrim($sbody, ",");
                $this->body = $sbody . "}";

            } else {
                $this->exit_error(500, print_r($db->errorInfo(), true));
            }
        }
        catch (PDOException $e) {
            $this->exit_error(500, $e->getMessage());
        }


    }
    function getStudentsByClassId($class_id)
    {
        try {

            $db = DemoDB::getConnection();

            $sql = "SELECT * FROM Student WHERE class_id=:class_id";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(":class_id", $class_id, PDO::PARAM_INT);


            $ok = $stmt->execute();
            $nb = $stmt->rowCount();
            $sbody = "{";


            if ($ok) {

                if ($nb == 0) {
                    $class_data = getClassData($_GET["class_id"]);
                    if (empty($class_data)) {
                        $this->exit_error(404, "class_idDoesNotExist");
                    }
                }

                if (isset($this->statusCode)) {
                    $this->statusCode = 200;
                }
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($row != null) {
                        if (isset($this->headers)) {
                            $this->headers[] = "Content-type: text/json; charset=utf-8";
                        }

                        // Produce utf8 encoded json

                        $sbody .= "\"" . $row["student_id"] . "\":" . json_encode($row) . ",";

                    } else {
                        $this->exit_error(404);
                    }
                }

                $sbody = rtrim($sbody, ",");
                $this->body = $sbody . "}";

            } else {
                $this->exit_error(500, print_r($db->errorInfo(), true));
            }
        }
        catch (PDOException $e) {
            $this->exit_error(500, $e->getMessage());
        }
    }


    function getStudentById()
    {
        try {

            $db = DemoDB::getConnection();
            $sql = "SELECT * FROM student WHERE student_id=:student_id";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(":student_id", $this->id);
            $ok = $stmt->execute();
            if ($ok) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $nb = $stmt->rowCount();
                if ($nb==0) {
                    $this->exit_error(404, 'studentNotFound');
                }
                if ($row != null) {
                    $this->statusCode = 200;
                    // Produce utf8 encoded json
                    $this->headers[] = "Content-type: text/json; charset=utf-8";
                    $this->body = json_encode($row);
                } else {
                    $this->exit_error(404);
                }
            } else {
                echo 'here';
                $this->exit_error(500, print_r($db->errorInfo(), true));
            }
        }
        catch (PDOException $e) {
            $this->exit_error(500, $e->getMessage());
        }


    }

    /** Is the request sent by an admin?
     * Very basic answer here: only user admin (password admin)
     * is admin. In realistic cases, we should access the DB.
     * @return type
     */
    protected function is_admin()
    {
        $result = true;
        /*   if (isset($_SERVER["PHP_AUTH_USER"])) {
        $result = $_SERVER["PHP_AUTH_USER"] == "admin"
        && $_SERVER["PHP_AUTH_PW"] == "admin";
        }
        */
        return $result;

    }

  
}


// Simply run the resource
StudentResource::run();
?>