<?php
$signaturesJson = file_get_contents("signatures.json");
$signatures = json_decode($signaturesJson, true);
?>
<html>
<head>
    <title>Liste des signatures DisMoi</title>
</head>
<body>
    <h1>Liste des signatures DisMoi</h1>
    <ul>
        <?php foreach ($signatures as $signature): ?>
        <li><a href="html.php?<?= http_build_query($signature); ?>"><?= $signature['firstName'] . ' ' . $signature['lastName']; ?></a> :  <?= $signature['role'] ?> / <?= $signature['tel']; ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>


