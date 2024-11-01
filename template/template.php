<?php
global $APPLICATION;
$APPLICATION->SetTitle("Таблица");

// Подключение к базе данных
$connection = Bitrix\Main\Application::getConnection();
$helper = $connection->getSqlHelper();

// Обработка запросов
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        $name = $_POST['name'];
        $connection->query("INSERT INTO test (name) VALUES ('$name')");
    }
    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $connection->query("UPDATE test SET name='$name' WHERE id='$id'");
    }
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $connection->query("DELETE FROM test WHERE id='$id'");
    }
}

// Получение данных
$result = $connection->query("SELECT * FROM test");
$items = $result->fetchAll();

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/custom/crud_table/tcpdf/tcpdf.php');

// Обработка запроса на экспорт в PDF
if (isset($_GET['export_pdf'])) {
    // Создаем новый PDF документ
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('export');
    $pdf->SetHeaderData('', 0, 'export data', '');
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->AddPage();

   
    $html = '<h1>export kap1c</h1><table border="1" cellpadding="4"><tr><th>ID</th><th>Имя</th></tr>';
    foreach ($items as $item) {
        $html .= '<tr><td>' . $item['id'] . '</td><td>' . $item['name'] . '</td></tr>';
    }
    $html .= '</table>';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('crud_table_data.pdf', 'D'); // D - для загрузки файла
    exit();
}
?>

<h1>CRUD Table</h1>
<form method="post">
    <input type="text" name="name" placeholder="Name">
    <button type="submit" name="create">Добавить</button>
</form>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?= $item['id'] ?></td>
            <td><?= $item['name'] ?></td>
            <td>
                <form method="post">
                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                    <input type="text" name="name" value="<?= $item['name'] ?>">
                    <button type="submit" name="update">Обновить</button>
                    <button type="submit" name="delete">Удалить</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<a href="?export_pdf=1">Экспорт в PDF</a>
