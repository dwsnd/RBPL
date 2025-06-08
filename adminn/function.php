<?php
$conn = mysqli_connect("localhost", "root", "", "petshop");

function query($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}
function tambah($data)
{
    global $conn;
    $name = htmlspecialchars($data["name"]);
    $price = htmlspecialchars($data["price"]);
    $category = htmlspecialchars($data["category"]);
    $stock = htmlspecialchars($data["stock"]);
    $description = htmlspecialchars($data["description"]);

    $image = upload();
    if (!$image) {
        return false;
    }

    $query = "INSERT INTO produk
             VALUES 
             ('','$name','$price','$image','$category','$stock','$description','','')
             ";

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function upload()
{
    $namaFile = $_FILES['g']['name'];
    $ukuranFile = $_FILES['gambar']['size'];
    $error = $_FILES['gambar']['error'];
    $tmpName = $_FILES['gambar']['tmp_name'];
    if ($error === 4) {
        echo "
            <script>
            alert('pilih gambar dulu ygy');
            </script>
        ";
        return false;
    }

    $ekstensiGambarValid = ['jpg', 'jpeg', 'png', 'webp'];
    $ekstensiGambar = explode('.', $namaFile);
    $ekstensiGambar = strtolower(end($ekstensiGambar));
    if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
        echo "
            <script>
            alert('bukan gambar banh!');
            </script>
        ";
    }
    if ($ukuranFile > 1000000) {
        echo "
            <script>
            alert('Ukuran kegeden');
            </script>
        ";
    }
    $namaFileBaru = uniqid();
    $namaFileBaru .= '.';
    $namaFileBaru .= $ekstensiGambar;
    move_uploaded_file($tmpName, 'img/' . $namaFileBaru);
    return $namaFileBaru;
}

function hapus($id)
{
    global $conn;
    mysqli_query($conn, "DELETE FROM produk WHERE id = $id");

    return mysqli_affected_rows($conn); 
}

function ubah($data)
{
    global $conn;
    $id = $data["id"];
    $name = htmlspecialchars($data["name"]);
    $price = htmlspecialchars($data["price"]);
    $category = htmlspecialchars($data["category"]);
    $stock = htmlspecialchars($data["stock"]);
    $description = htmlspecialchars($data["description"]);
    $gambarLama = htmlspecialchars($data["gambarLama"]);
    if($_FILES['image']['error']===4){
        $image = $gambarLama;
    } else {
        $image = upload();
    }
    $query = "UPDATE produk SET 
            name = '$name',
            price = '$price',
            category = '$category',
            stock = '$stock',
            description = '$description',
            image = '$image'
            WHERE id = $id
            ";
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}
