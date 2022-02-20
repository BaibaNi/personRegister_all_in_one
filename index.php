<style><?php include 'registryStyle.css';

require_once 'vendor/autoload.php';
use Doctrine\DBAL\DriverManager;

//--- connection to the database
try {
    $connectionParams = array(
        'dbname' => 'person_register',
        'user' => 'banibai',
        'password' => 'Learning_mysql_074',
        'host' => 'localhost',
        'driver' => 'pdo_mysql',
    );

    $conn = DriverManager::getConnection($connectionParams);
} catch (\Doctrine\DBAL\Exception $e) {
    echo 'Error! ' . $e->getMessage() . PHP_EOL;
    die();
}

function checkIfCodeUnique($conn, string $code): bool {
    $status = '';
    foreach ($conn->iterateAssociativeIndexed(
        'SELECT id, name, surname, code FROM person_register.persons') as $data) {
        if ($code !== $data['code']) {
            $status = true;
        }else{
            $status = false;
        }
    }
    return $status;
}


$errorMsg = '';

if(isset($_POST['submit'])) {

//--- Grabbing the data
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $code = $_POST['code'];


    if(empty($name)){
        $errorMsg = 'Unable to register this person. Name is required.';
    } elseif (empty($surname)){
        $errorMsg = 'Unable to register this person. Surname is required.';
    } elseif (empty($code)){
        $errorMsg = 'Unable to register this person. Personal ID code is required.';
    } elseif(checkIfCodeUnique($conn, $code)) {
        $errorMsg = 'Unable to register. Person with such Personal ID code already exists in the Database.';
    } else{
        $registry = [
            'name' => $name,
            'surname' => $surname,
            'code' => $code
        ];

//--- data being recorded in the table
        try {
            $conn->insert('person_register.persons', $registry);
            $errorMsg = 'Registration successful!';
        } catch (\Doctrine\DBAL\Exception $e) {
            echo 'Error! ' . $e->getMessage() . PHP_EOL;
            die();
        }
    }
}

?></style>


<!doctype html>
<html lang="en">
<head>
    <title>PERSON REGISTER</title>
</head>


<body>
<h1><b>PERSON REGISTER</b></h1>

<div class='alert'><?php echo $errorMsg; ?></div>

<section class="getdata">
    <form method="post">
        <label> Name: <input type="text" name="name" placeholder="Name"></label>
        <label> Surname: <input type="text" name="surname" placeholder="Surname"></label>
        <label> ID code: <input type="text" name="code" placeholder="ID code"></label>
        <button type="submit" name="submit">Register</button>
    </form>
</section>

<section>
    <table>
        <tr style="background-color: lightcoral">
            <th>Nr.</th>
            <th>Name</th>
            <th>Surname</th>
            <th>Personal ID code</th>
            <th>DataBase ID</th>
        </tr>
        <?php
        $i=1;
        foreach($conn->iterateAssociativeIndexed(
                'SELECT id, name, surname, code FROM person_register.persons') as $id => $data): ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $data['name']; ?></td>
                <td><?php echo $data['surname']; ?></td>
                <td><?php echo $data['code']; ?></td>
                <td><?php echo $id; ?></td>
            </tr>
        <?php $i++;
        endforeach; ?>
    </table>
</section>

</body>
</html>