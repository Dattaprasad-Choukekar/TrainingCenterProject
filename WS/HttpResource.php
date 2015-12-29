<?php
 /** 
  * <p>Ancêtre des ressources utilisées en Web Services REST.
  * Répond aux méthodes
  * GET, PUT, DELETE, POST et HEAD en positionnant le statut
  * via $statusCode, les en-têtes de la réponse via $headers,
  * et le corps de la réponse via $body.
  * <br/>Par défaut, renvoie "405, method not allowed".</p>
  * <p>Usage : faire hériter sa classe de HttpResource, et lancer
  * la méthode <code>run</code>. Exemple :</p>
  * <pre>
  * &lt;?php  
  * class MaRessource extends HttpResource {
  *   function do_get() {
  *     // votre code ici
  *     $this->statusCode = ...;
  *     $this->headers = ...;
  *     $this->body = ...;
  *   }
  *   // Même chose pour do_put, do_post etc.
  * }
  * MaRessource::run();
  * ?></pre>
  * <p><code>run</code> instancie la classe, exécute la procédure 
  * <code>init</code> puis le do_get, do_put etc. en fonction de 
  * la méthode HTTP de la requête.</p>
  * Par défaut, GET et HEAD renvoient 200, PUT et DELETE 204, POST 201.
  * <p>Requiert PHP 5.3 minimum (pour instancier l'objet dans la 
  * procédure <code>run</code>. Si vous avez une version antérieure,
  * voyez le commentaire sur la méthode run.</p>
  */
class HttpResource {
  /** Statut HTTP de la réponse (200, 201, 204, 400, 401, 404, 409 etc.) 
  * <br/>Par défaut, 200 en GET et HEAD, 201 en POST, et 204 en PUT et DELETE.
  */
  protected $statusCode;

  /** En-têtes de la réponse */
  protected $headers = array();

  /** Corps de la réponse. Par defaut, vide. */
  protected $body = "";

  /** Initialiser la ressource. Par exemple, récupérer le id dans l'URL si
   * la resource est censée en contenir. Par défaut, ne fait rien.
   */
  protected function init() {
  }
  
  /** Réponse à un DELETE. Par défaut, renvoie 405 (pas implémenté) */
  protected function do_delete() {
    $this->statusCode = 405;
  }

  /** Réponse à un GET. Par défaut, renvoie 405 (pas implémenté) */
  protected function do_get() {
    $this->statusCode = 405;
  }

  /** Réponse à un HEAD. Par défaut, renvoie 405 (pas implémenté) */
  protected function do_head() {
    $this->statusCode = 405;
  }

  /** Réponse à un POST. Par défaut, renvoie 405 (pas implémenté) */
  protected function do_post() {
    $this->statusCode = 405;
  }

  /** Réponse à un PUT. Par défaut, renvoie 405 (pas implémenté) */
  protected function do_put() {
    $this->statusCode = 405;
  }

  /** Envoie la ligne de statut avec le code indiqué et le message
    * standard associé 
    */
  public static function send_status($codeHttp) {
    header("HTTP/1.1 $codeHttp ".self::$http_codes[$codeHttp]);
  }

  /** Envoie la ligne de statut avec le code indiqué, met dans
    * le corps de la réponse le message (optionnel), et envoie
    * la réponse
    */
  public static function exit_error($codeHttp, $message = "") {
    self::send_status($codeHttp);
    die($message);
  }

  /** Réponse à une requête */
  // Si PHP < 5.3, enlever static, remplacer instance par this, commencer a $this->init,
  // et ensuite, dans les classes descendantes, lancer run dans le constructeur,
  // et terminer la page de chaque ressource MaResource par new MaResource().
  // C'est un peu plus lourd, mais ça contourne l'absence de get_called_class.
  public static function run() {
    // Instancier la classe qui herite de cette classe
    $className = get_called_class();
    $instance = new $className;
    // Initialiser l'instance
    $instance->init();
    // Repondre a la requete
    switch ($_SERVER['REQUEST_METHOD']) {
      case "DELETE":
        // Valeur si ok
        $instance->statusCode = 204;
        $instance->do_delete();
        break;
      case "GET": 
        // Valeur si ok
        $instance->statusCode = 200;
        $instance->do_get();
        break;
      case "HEAD":
        // Valeur si ok
        $instance->statusCode = 200;
        $instance->do_head();
        break;
      case "POST":
        // Valeur si ok
        $instance->statusCode = 201;
        $instance->do_post();
        break;
      case "PUT":
        // Valeur si ok
        $instance->statusCode = 204;
        $instance->do_put();
        break;
      default:
        $instance->statusCode = 405;
    }
    $instance->send_status($instance->statusCode);
    foreach ($instance->headers as $i => $header) {
      header($header);
    }
    print $instance->body;
  }
  
  /** Codes retour HTTP. Les plus utiles : 
    * 200 => "OK",
    * 201 => "Created",
    * 204 => "No content",
    * 400 => "Bad request",
    * 401 => "Unauthorized",
    * 404 => "Not found",
    * 405 => "Method not allowed",
    * 409 => "Conflict",
    * 500 => "Internal server error");
    */
  protected static $http_codes = array(
    100 => 'Continue',
    101 => 'Switching Protocols',
    102 => 'Processing',
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    207 => 'Multi-Status',
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    306 => 'Switch Proxy',
    307 => 'Temporary Redirect',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Requested Range Not Satisfiable',
    417 => 'Expectation Failed',
    418 => 'I\'m a teapot',
    422 => 'Unprocessable Entity',
    423 => 'Locked',
    424 => 'Failed Dependency',
    425 => 'Unordered Collection',
    426 => 'Upgrade Required',
    449 => 'Retry With',
    450 => 'Blocked by Windows Parental Controls',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Timeout',
    505 => 'HTTP Version Not Supported',
    506 => 'Variant Also Negotiates',
    507 => 'Insufficient Storage',
    509 => 'Bandwidth Limit Exceeded',
    510 => 'Not Extended'
  );
}
 
