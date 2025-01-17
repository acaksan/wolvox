<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Yönetim Paneli'); ?></title>
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($title ?? 'Yönetim Paneli'); ?></h1>
        <?php renderMenu(); ?>
        <hr>
    </header>
