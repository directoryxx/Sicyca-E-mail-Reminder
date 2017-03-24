<?php
require_once('config.php');
require_once ('phpmailer/PHPMailerAutoload.php');
include 'simplehtmldom/simple_html_dom.php';

class sicyca
{

//===========================  Diganti =========================================
  private $useremail = "xxxx@gmail.com";

  //Password to use for SMTP authentication
  private $passemail = "xxxx";

  //PIN STIKOM
  private $password = 'xxxx';

  //NIM STIKOM
  private $username = 'xxxx';

  //Kirim Email Ke
  private $emailto = 'directoryx@tutanota.com';

  //Nama Kamu
  private $nama = 'Anak Agung Angga Wijaya';

  //URL Load MK
  private $mdata= 'http://localhost/projecttemp/uploadthisnow/config/m-data.php';
//==============================================================================


//=========================== Jangan Diganti ===================================
  //URL LOGIN SICYCA
  private $loginUrl = 'https://sicyca.stikom.edu/?login';

  //URL Sicyca Jadwal MK
  private $jadwal= 'https://sicyca.stikom.edu/akademik';

  //URL Sicyca Pinjaman Buku
  private $buku = 'https://sicyca.stikom.edu/perpustakaan/peminjaman';
//==============================================================================


  private $connsti;
  private $conn;

  public function __construct()
  {
    $database = new Database();
    $db = $database->dbConnection();
    $this->connsti = $db;
    $this->conn = $db;
    }




  public function sinMK()
  {
    $html = new simple_html_dom();
    $password = $this->password;
    $username = $this->username;
    $loginUrl = $this->loginUrl;

    //init curl
    $ch = curl_init();

    //Set the URL to work with
    curl_setopt($ch, CURLOPT_URL, $loginUrl);

    // ENABLE HTTP POST
    curl_setopt($ch, CURLOPT_POST, 1);

    //Set the post parameters
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'nim='.$username.'&pin='.$password);

    //Handle cookies for the login
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');

    //Setting CURLOPT_RETURNTRANSFER variable to 1 will force cURL
    //not to print out the results of its query.
    //Instead, it will return the results as a string return value
    //from curl_exec() instead of the usual true/false.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //execute the request (the login)
    $store = curl_exec($ch);

    //the login is now done and you can continue to get the
    //protected content.

    //set the URL to the protected file
    curl_setopt($ch, CURLOPT_URL, $this->jadwal);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds
    //execute the request
    $content = curl_exec($ch);

    curl_close($ch);

    //save the data to disk
    $get = file_put_contents('temp/tempdashboard.html',$content);
    //echo file_get_contents('dashboard.html');

    $html->load("temp/tempdashboard.html");

    // get the table. Maybe there's just one, in which case just 'table' will do
    $html = file_get_html('temp/tempdashboard.html');
    foreach($html->find('table.sicycatable tr td') as $e){
             $arr[] = trim($e->innertext);

     }


      for ($x = 0; $x <= end(array_keys($arr)); $x+=5) {
        $a = $arr[$x+1];
        $b = $arr[$x+2];
        $c = $arr[$x+3];
        $d = $arr[$x+4];
        $asd = $arr[$x];
      try {

        $stmt = $this->connsti->prepare("INSERT INTO mk(hari,jam,ruang,matakuliah,keterangan) VALUES
        (:hari, :jam, :ruang, :matakuliah, :keterangan)");
        $stmt->bindparam(":hari",$asd);
        $stmt->bindparam(":jam",$a);
        $stmt->bindparam(":ruang",$b);
        $stmt->bindparam(":matakuliah",$c);
        $stmt->bindparam(":keterangan",$d);
        $stmt->execute();
      } catch (Exception $e) {
        echo $e->getMessage();
        return false;
      }
      }

  }

  public function cekrow()
  {
    $stmt = $this->connsti->prepare('SELECT COUNT(*) FROM mk ');
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return true;
  }



  public function loadmk()
  {
    if ($this->cekrow()>0) {
      $stmt = $this->connsti->prepare("TRUNCATE TABLE mk");
      $stmt->execute();
      $this->sinMK();

          $load = $this->connsti->prepare("SELECT * FROM mk");
          $load->execute();

          if ($load->rowCount()>0) {
            unlink("temp/tempdashboard.html");
            while ($row=$load->fetch(PDO::FETCH_ASSOC)) {
              ?>
              <tr>
                <td><?php print($row['hari']); ?></td>
                <td><?php print($row['jam']); ?></td>
                <td><?php print($row['ruang']); ?></td>
                <td><?php print($row['matakuliah']); ?></td>
                <td><?php print($row['keterangan']); ?></td>
              </tr>
              <?php
            }
          }
          else {
            ?>
            <tr>
              <td>Nothing here...</td>
            </tr>
            <?php
          }
    }

  }
  public function send($hariini)
  {
    $datetime = new DateTime('today');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->mdata);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //execute the request
    $content = curl_exec($ch);

    curl_close($ch);

    //save the data to disk
    $get = file_put_contents('temp/tempdashboard.html',$content);
    $mail = new PHPMailer;
    //Tell PHPMailer to use SMTP
    $mail->isSMTP();

    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 0;

    //Ask for HTML-friendly debug output
    $mail->Debugoutput = 'html';

    //Set the hostname of the mail server
    $mail->Host = 'smtp.gmail.com';
    // use
    // $mail->Host = gethostbyname('smtp.gmail.com');
    // if your network does not support SMTP over IPv6

    //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
    $mail->Port = 587;

    //Set the encryption system to use - ssl (deprecated) or tls
    $mail->SMTPSecure = 'tls';

    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;

    //Username to use for SMTP authentication - use full email address for gmail
    $mail->Username = $this->useremail;

    //Password to use for SMTP authentication
    $mail->Password = $this->passemail;

    //Set who the message is to be sent from
    $mail->setFrom('from@example.com', 'Bot Sicyca');

    //Set an alternative reply-to address
    $mail->addReplyTo('16410100123@stikom.edu', 'Anak Agung Angga Wijaya');

    //Set who the message is to be sent to
    $mail->addAddress($this->emailto, $this->nama);

    $mail->addAddress('directoryx@tutanota.com', 'Anak Agung Angga Wijaya');

    //Set the subject line
    $mail->Subject = 'Jadwal Sicyca '.$hariini;

    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    $mail->msgHTML(file_get_contents('temp/tempdashboard.html'), dirname(__FILE__));

    //$mail->Body = file_get_contents('temp/tempdashboard.html');

    //Replace the plain text body with one created manually
    $mail->AltBody = 'This is a plain-text message body';

    //send the message, check for errors
    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        echo "Message sent!";
        unlink('temp/tempdashboard.html');
    }
  }

  public function sinPERPUS()
  {
    $html = new simple_html_dom();
    $password = $this->password;
    $username = $this->username;
    $loginUrl = $this->loginUrl;

    //init curl
    $ch = curl_init();

    //Set the URL to work with
    curl_setopt($ch, CURLOPT_URL, $loginUrl);

    // ENABLE HTTP POST
    curl_setopt($ch, CURLOPT_POST, 1);

    //Set the post parameters
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'nim='.$username.'&pin='.$password);

    //Handle cookies for the login
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');

    //Setting CURLOPT_RETURNTRANSFER variable to 1 will force cURL
    //not to print out the results of its query.
    //Instead, it will return the results as a string return value
    //from curl_exec() instead of the usual true/false.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //execute the request (the login)
    $store = curl_exec($ch);

    //the login is now done and you can continue to get the
    //protected content.

    //set the URL to the protected file
    curl_setopt($ch, CURLOPT_URL, $this->buku);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds
    //execute the request
    $content = curl_exec($ch);

    curl_close($ch);

    //save the data to disk
    $get = file_put_contents('temp/temppinjam.html',$content);
    //echo file_get_contents('dashboard.html');

    $html->load("temp/temppinjam.html");

    // get the table. Maybe there's just one, in which case just 'table' will do
    $html = file_get_html('temp/temppinjam.html');
    foreach($html->find('table.keuangan tbody tr td') as $e){
             $arr1[] = trim($e->innertext);
     }

    do {
      $b = $arr1[$i+2];
      $c = $arr1[$i+3];
      $d = $arr1[$i+4];
      $f = $arr1[$i+6];
      $a = $arr1[$i];
      try {
        $stmt = $this->connsti->prepare("INSERT INTO perpus(judul,pinjam,kembali,denda) VALUES
        (:judul, :pinjam, :kembali, :denda)");
        $stmt->bindparam(":judul",$b);
        $stmt->bindparam(":pinjam",$c);
        $stmt->bindparam(":kembali",$d);
        $stmt->bindparam(":denda",$f);
        $stmt->execute();
      } catch (Exception $e) {
        echo $e->getMessage();
        return false;
      }
      $i+=5;
    } while ($a != NULL);
/*
     $max_iterations = 100;

      for ($i=0;$i <=$max_iterations;$i++)
      {
        $b = $arr1[$i+2];
        $c = $arr1[$i+3];
        $d = $arr1[$i+4];
        $f = $arr1[$i+6];
        $a = $arr1[$i];
        if ($a !=NULL)
        try {
          $stmt = $this->connsti->prepare("INSERT INTO perpus(judul,pinjam,kembali,denda) VALUES
          (:judul, :pinjam, :kembali, :denda)");
          $stmt->bindparam(":judul",$b);
          $stmt->bindparam(":pinjam",$c);
          $stmt->bindparam(":kembali",$d);
          $stmt->bindparam(":denda",$f);
          $stmt->execute();
        } catch (Exception $e) {
          echo $e->getMessage();
          return false;
        }
      else
        break;
      }
      */
/*
      for ($x = 0; $x <= $myLastElement; $x+=5) {
        $b = $arr1[$x+2];
        $c = $arr1[$x+3];
        $d = $arr1[$x+4];
        $f = $arr1[$x+6];
        $a = $arr1[$x];
          try {
            echo count($arr1);
            $stmt = $this->connsti->prepare("INSERT INTO perpus(judul,pinjam,kembali,denda) VALUES
            (:judul, :pinjam, :kembali, :denda)");
            $stmt->bindparam(":judul",$b);
            $stmt->bindparam(":pinjam",$c);
            $stmt->bindparam(":kembali",$d);
            $stmt->bindparam(":denda",$f);
            $stmt->execute();
          } catch (Exception $e) {
            echo $e->getMessage();
            return false;
          }

      }
*/
  }
  public function loadperpus()
  {
    if ($this->cekrow()>0) {
      $stmt = $this->connsti->prepare("TRUNCATE TABLE perpus");
      $stmt->execute();
      $this->sinPERPUS();

          $load = $this->connsti->prepare("SELECT * FROM perpus");
          $load->execute();

          if ($load->rowCount()>0) {
            unlink("temp/temppinjam.html");
            while ($row=$load->fetch(PDO::FETCH_ASSOC)) {
              ?>
              <tr>
                <td><?php print($row['judul']); ?></td>
                <td><?php print($row['pinjam']); ?></td>
                <td><?php print($row['kembali']); ?></td>
                <td><?php print($row['denda']); ?></td>
              </tr>
              <?php
            }
          }
          else {
            ?>
            <tr>
              <td>Nothing here...</td>
            </tr>
            <?php
          }
    }

  }


}


?>
