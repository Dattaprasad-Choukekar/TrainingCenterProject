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

class TeamResource extends HttpResource
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
            if (isset($_GET["team_owner_id"])) {
                if (is_numeric($_GET["team_owner_id"])) {
                    $this::getTeamsByOwnerId();
                } else {
                    $this->exit_error(400, "idNotPositiveInteger");
                }
            }
        }


    }

    public function getTeamsByOwnerId()
    {
        try {
            $db = DemoDB::getConnection();
            $sql = "SELECT * FROM team WHERE team_owner_id=:team_owner_id";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(":team_owner_id", $_GET["team_owner_id"], PDO::PARAM_INT);
            $ok = $stmt->execute();
            if ($ok) {
                $nb = $stmt->rowCount();
                if ($nb == 0) {
                    $db = DemoDB::getConnection();
                    $sql = "SELECT * FROM student WHERE student_id=:student_id";
                    $stmt2 = $db->prepare($sql);
                    $stmt2->bindValue(":student_id", $_GET["team_owner_id"], PDO::PARAM_INT);
                    $ok = $stmt2->execute();
                    $nb = $stmt2->rowCount();
                    if ($nb == 0) {
                        // Student does not exist
                        $this->exit_error(404);
                    }
                    // student exists but no team

                    //$this->exit_error(404);
                }
                $sbody = "{";
                $this->statusCode = 200;
                $this->headers[] = "Content-type: text/json; charset=utf-8";

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($row != null) {
                        $sbody .= "\"" . $row["team_id"] . "\":" . json_encode($row) . ",";
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


    /** Is the request sent by an admin?
     * Very basic answer here: only user admin (password admin)
     * is admin. In realistic cases, we should access the DB.
     * @return type
     */
    protected function is_admin()
    {
        $result = true;
        if (isset($_SERVER["PHP_AUTH_USER"])) {
            $result = $_SERVER["PHP_AUTH_USER"] == "admin" && $_SERVER["PHP_AUTH_PW"] ==
                "admin";
        }
        return $result;

    }

    protected function do_put()
    {
        if (!$this->is_admin()) {
            $this->exit_error(401, "mustBeAdmin");
        }
        // Les parametres passes en put
        parse_str(file_get_contents("php://input"), $_PUT);
        if (empty($_PUT["name"])) {
            $this->exit_error(400, "nameMandatoryAndNotEmpty");
        }  if (empty($_PUT["summary"])) {
            $this->exit_error(400, "summaryMandatoryAndNotEmpty");
        } if (empty($_PUT["project_id"])) {
            $this->exit_error(400, "project_idMandatoryAndNotEmpty");
        } if (empty($_PUT["team_owner_id"])) {
            $this->exit_error(400, "team_owner_idMandatoryAndNotEmpty");
        }else {
            try {
                $db = DemoDB::getConnection();
                $sql = "INSERT INTO team (name, summary, project_id, team_owner_id) values (:name, :summary, :project_id, :team_owner_id)";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":name", ucwords(trim($_PUT["name"])));
                $stmt->bindValue(":summary", ucwords(trim($_PUT["summary"])));
                $stmt->bindValue(":project_id", $_PUT["project_id"]);
                $stmt->bindValue(":team_owner_id", $_PUT["team_owner_id"]);
                $ok = $stmt->execute();
                if ($ok) {
                    $this->statusCode = 204;
                    $this->body = "";
                    // Number of affected rows
                } else {
                    $erreur = $stmt->errorInfo();
                    print_r( $erreur);
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
        if (empty($_GET["team_owner_id"])) {
            $this->exit_error(400, "team_owner_idRequired");
        }
        try {
            $db = DemoDB::getConnection();
            $sql = "DELETE FROM team WHERE team_id=:id and team_owner_id=:team_owner_id";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(":id", $this->id);
            $stmt->bindValue(":team_owner_id", $_GET["team_owner_id"]);
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

// Simply run the resource
TeamResource::run();
?>