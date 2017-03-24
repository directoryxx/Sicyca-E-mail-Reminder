<?php
//ini_set('display_errors', 'On');
//ini_set('html_errors', 0);
//error_reporting(-1);
error_reporting(0);

require_once 'class.sicyca.php';
$sicyca = new sicyca();



?>

<div id="page-content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1>Pinjam Buku</h1>

                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Hari, Tanggal</th>
                        <th>Jam</th>
                        <th>Ruangan</th>
                        <th>Nama Matakuliah</th>
                        <th>Keterangan</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sicyca->loadperpus();
                      ?>
                    </tbody>
                  </table>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>
