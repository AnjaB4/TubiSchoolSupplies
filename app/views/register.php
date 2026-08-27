<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Register - Tubi</title>
    <link rel="stylesheet" href="/public/style.css" />
</head>
<body>
    <h1>Register</h1>

    <?php if (!empty($error)): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <p class="success-message"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="username">Username:</label><br />
        <input type="text" id="username" name="username" required><br /><br />

        <label for="email">Email:</label><br />
        <input type="email" id="email" name="email" required><br /><br />

        <label for="school_name">School Name:</label><br />
        <input type="text" id="school_name" name="school_name" required><br /><br />

        <label for="school_address">School Address:</label><br />
        <input type="text" id="school_address" name="school_address" required><br /><br />

        <label for="district">District:</label><br />
        <input type="text" id="district" name="district" required><br /><br />

        <label for="city">City:</label><br />
        <input type="text" id="city" name="city" required><br /><br />

        <label for="password">Password:</label><br />
        <input type="password" id="password" name="password" required><br /><br />

        <label for="confirm">Confirm Password:</label><br />
        <input type="password" id="confirm" name="confirm" required><br /><br />

        <button type="submit">Register</button>
    </form>
</body>
</html>
