  <?php
  try {
    /* using composer */
    require_once __DIR__ . './../vendor/autoload.php';
    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable("./../");
    $dotenv->load();
    /* $env = parse_int_file(__DIR__ , "../.env"); manual parsing*/ 

    $host = $_ENV[DB_HOST];
    $dbname = $_ENV[DB_NAME];
    $user = $_ENV[DB_USER];
    $pasword = $_ENV[DB_PASS];

    $conn = new PDO("mysql:host=$",$host, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRORMODE, PDO::ATTR_ERRORMODE_EXCEPTION);
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci";

    $conn->exec($sql);
    echo "connected to database.";
  } catch(PDOException $e){
    echo $e->getMessage();
  }
?>
