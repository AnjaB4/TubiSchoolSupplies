<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login - Tubi</title>
    <link rel="stylesheet" href="/style.css" />
</head>
<body>
    <a class="back-btn" href="/home">Početna</a>

    <h1>Login</h1>

    <?php if (!empty($error)): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="/auth/login">
        <label for="username">Username:</label><br />
        <input type="text" id="username" name="username" required><br /><br />

        <label for="password">Password:</label><br />
        <input type="password" id="password" name="password" required><br /><br />

        <button type="submit">Login</button>
    </form>
</body>
</html>
