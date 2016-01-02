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

class ProjectResource extends HttpResource
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
                    $this->getAllProjectsByOwnerIdOrClassId(null);
                    return;
                } else {
                    $this->exit_error(400, "owner_idNotPositiveInteger");
                }
            }
            if (isset($_GET["class_id"])) {
                if (is_numeric($_GET["class_id"])) {
                    $this->getAllProjectsByOwnerIdOrClassId($_GET["class_id"]);
                    return;
                } else {
                    $this->exit_error(400, "class_idNotPositiveInteger");
                }
            }

            $this->getAllProjects();
        } else {
            $this->getProjectsyId();

        }
    }

    function getAllProjects()
    {
        try {

            $db = DemoDB::getConnection();
            $sql = "SELECT id, title, subject, creation_datetime, deadline, class_id, owner_id FROM Project";
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
                        $sbody .= "\"" . $row["id"] . "\":" . json_encode($row) . ",";
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
    function getAllProjectsByOwnerIdOrClassId($class_id)
    {
        try {

            $db = DemoDB::getConnection();

            if ($class_id != null) {

                $sql = "SELECT id, title, subject, creation_datetime, deadline, class_id, owner_id FROM Project WHERE class_id=:class_id";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":class_id", $class_id, PDO::PARAM_INT);

            } else {

                $sql = "SELECT id, title, subject, creation_datetime, deadline, class_id, owner_id FROM Project WHERE owner_id=:owner_id";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":owner_id", $_GET["owner_id"], PDO::PARAM_INT);

            }

            $ok = $stmt->execute();
            $nb = $stmt->rowCount();
            $sbody = "{";


            if ($ok) {

                if ($nb == 0) {
                    if ($class_id == null) {
                        $trainer_data = getTrainerData($_GET["owner_id"]);
                        if (empty($trainer_data)) {
                            $this->exit_error(404, "owner_idDoesNotExist");
                        }
                    } else {
                        $class_data = getClassData($_GET["class_id"]);
                        if (empty($class_data)) {
                            $this->exit_error(404, "class_idDoesNotExist");
                        }
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

                        $sbody .= "\"" . $row["id"] . "\":" . json_encode($row) . ",";

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


    function getProjectsyId()
    {
        try {

            $db = DemoDB::getConnection();
            $sql = "SELECT id, title, subject, creation_datetime, deadline, class_id, owner_id FROM Project WHERE id=:project_id";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(":project_id", $this->id);
            $ok = $stmt->execute();
            if ($ok) {

                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row != null) {
                    $this->statusCode = 200;
                    // Produce utf8 encoded json
                    $this->headers[] = "Content-type: text/json; charset=utf-8";
                    $this->body = json_encode($row);
                } else {
                    $this->exit_error(404);
                }
            } else {
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

    protected function do_post()
    {
        if (!$this->is_admin()) {
            $this->exit_error(401, "mustBeAdmin");
        }
        // Les parametres passes en put
        parse_str(file_get_contents("php://input"), $_PUT);
        if (empty($_PUT["title"])) {
            $this->exit_error(400, "titleMandatoryAndNotEmpty");
        } else
            if (empty($_PUT["subject"])) {
                $this->exit_error(400, "subjectMandatoryAndNotEmpty");
            } else {
                try {
                    $db = DemoDB::getConnection();
                    $sql = "UPDATE project set title=:title, subject=:subject, deadline=:deadline where id=:id";
                    $stmt = $db->prepare($sql);
                    $stmt->bindValue(":id", ucwords(trim($_GET["id"])));
                    $stmt->bindValue(":title", ucwords(trim($_PUT["title"])));
                    $stmt->bindValue(":subject", ucwords(trim($_PUT["subject"])));
                    if (isset($_PUT["deadline"])) {
                        $stmt->bindValue(":deadline", trim($_PUT["deadline"]));
                    } else {
                        $stmt->bindValue(":deadline", '');
                    }


                    $ok = $stmt->execute();
                    if ($ok) {
                        $this->statusCode = 204;
                        $this->body = "";
                        // Number of affected rows
                        $nb = $stmt->rowCount();
                        if ($nb == 0) {
                            // No person or not really changed.
                            // Check it;
                            $sql = "SELECT id FROM project WHERE id=:id";
                            $stmt = $db->prepare($sql);
                            $stmt->bindValue(":id", $_GET["id"]);
                            $ok = $stmt->execute();
                            if ($stmt->fetch() == null) {
                                $this->exit_error(404, "project_idDoesNotExist");
                            }
                        }

                    } else {
                        $erreur = $stmt->errorInfo();
                        // si doublon
                        if ($erreur[1] == 1062) {
                            $this->exit_error(409, "duplicateName");
                        } else {
                            $this->exit_error(409, $erreur[1] . " : " . $erreur[2]);
                        }
                    }
                }
                catch (PDOException $e) {
                    $this->exit_error(500, $e->getMessage());
                }
            }
    }

    protected function do_put()
    {
        if (!$this->is_admin()) {
            $this->exit_error(401, "mustBeAdmin");
        }
        // Les parametres passes en put
        parse_str(file_get_contents("php://input"), $_PUT);
        if (empty($_PUT["title"])) {
            $this->exit_error(400, "titleMandatoryAndNotEmpty");
        } else
            if (empty($_PUT["subject"])) {
                $this->exit_error(400, "subjectMandatoryAndNotEmpty");
            } else
                if (empty($_PUT["class_id"])) {
                    $this->exit_error(400, "classIdMandatoryAndNotEmpty");
                } else
                    if (empty($_PUT["owner_id"])) {
                        $this->exit_error(400, "ownerIdMandatoryAndNotEmpty");
                    } else {
                        try {
                            $db = DemoDB::getConnection();
                            $sql = "INSERT INTO project (title, subject, deadline, class_id, owner_id) VALUES ( :title, :subject, :deadline, :class_id, :owner_id);";
                            $stmt = $db->prepare($sql);
                            $stmt->bindValue(":title", ucwords(trim($_PUT["title"])));
                            $stmt->bindValue(":subject", ucwords(trim($_PUT["subject"])));
                            $stmt->bindValue(":class_id", trim($_PUT["class_id"]), PDO::PARAM_INT);
                            $stmt->bindValue(":owner_id", trim($_PUT["owner_id"]), PDO::PARAM_INT);
                            $stmt->bindValue(":deadline", trim($_PUT["deadline"]));
                            $ok = $stmt->execute();
                            if ($ok) {
                                $this->statusCode = 204;
                                $this->body = "";
                                // Number of affected rows
                                $nb = $stmt->rowCount();
                                if ($nb == 0) {
                                    // No person or not really changed.
                                    // Check it;
                                    $sql = "SELECT person_id FROM person WHERE person_id=:id";
                                    $stmt = $db->prepare($sql);
                                    $stmt->bindValue(":id", $_GET["id"]);
                                    $ok = $stmt->execute();
                                    if ($stmt->fetch() == null) {
                                        $this->exit_error(404);
                                    }
                                }
                            } else {
                                $erreur = $stmt->errorInfo();
                                // si doublon
                                if ($erreur[1] == 1062) {
                                    $this->exit_error(409, "duplicateName");
                                } else {
                                    $this->exit_error(409, $erreur[1] . " : " . $erreur[2]);
                                }
                            }
                        }
                        catch (PDOException $e) {
                            $this->exit_error(500, $e->getMessage());
                        }
                    }
    }

    protected function do_delete()
    {
        if (!$this->is_admin()) {
            $this->exit_error(401);
        }
        if (empty($_GET["id"])) {
            $this->exit_error(400, "idRequired");
        }
        try {
            $db = DemoDB::getConnection();
            $sql = "DELETE FROM person WHERE person_id=:id";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(":id", $this->id);
            $ok = $stmt->execute();
            if ($ok) {
                $this->statusCode = 204;
                $this->body = "";
                $nb = $stmt->rowCount();
                if ($nb == 0) {
                    $this->exit_error(404);
                }
            } else {
                $erreur = $stmt->errorInfo();
                $this->exit_error(409, $erreur[1] . " : " . $erreur[2]);
            }
        }
        catch (PDOException $e) {
            $this->exit_error(500, $e->getMessage());
        }
    }
}


function getTrainerData($trainer_id)
{

    try {
        $db = DemoDB::getConnection();
        $sql = "SELECT * from trainer where id=:id;";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id", $trainer_id, PDO::PARAM_INT);
        $ok = $stmt->execute();
        if ($ok) {
            $nb = $stmt->rowCount();
            if ($nb == 0) {
                // invalid tariner id
                return array();
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row != null) {
                return $row;
            } else {
                return null;
                ;
            }

        } else {
            echo 'error while getting student class information';
            return null;
            ;
        }
    }
    catch (PDOException $e) {
        print_r($e->getMessage());
        return null;
    }

}

function getClassData($class_id)
{

    try {
        $db = DemoDB::getConnection();
        $sql = "SELECT * from class where id=:id;";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":id", $class_id, PDO::PARAM_INT);
        $ok = $stmt->execute();
        if ($ok) {
            $nb = $stmt->rowCount();
            if ($nb == 0) {
                // invalid tariner id
                return array();
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row != null) {
                return $row;
            } else {
                return null;
                ;
            }

        } else {
            echo 'error while getting student class information';
            return null;
            ;
        }
    }
    catch (PDOException $e) {
        print_r($e->getMessage());
        return null;
    }

}


// Simply run the resource
ProjectResource::run();
?>