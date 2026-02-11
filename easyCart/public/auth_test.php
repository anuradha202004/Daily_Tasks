<!DOCTYPE html>
<html>
<head>
    <title>Authentication Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .success {
            background: #4CAF50;
            color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .info {
            background: white;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
            border-left: 4px solid #2196F3;
        }
    </style>
</head>
<body>
    <div class="success">
        <h1>✅ Authentication Successful!</h1>
        <p>You are logged in as: <strong><?php echo htmlspecialchars($_SERVER['PHP_AUTH_USER']); ?></strong></p>
    </div>
    
    <div class="info">
        <h2>Access Information:</h2>
        <p><strong>Your IP:</strong> <?php echo $_SERVER['REMOTE_ADDR']; ?></p>
        <p><strong>Server IP:</strong> <?php echo $_SERVER['SERVER_ADDR']; ?></p>
        <p><strong>Access URL:</strong> <?php echo $_SERVER['REQUEST_URI']; ?></p>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($_SERVER['PHP_AUTH_USER']); ?></p>
    </div>
    
    <div class="info">
        <h2>Next Steps:</h2>
        <p>The password protection is working! You can now:</p>
        <ul>
            <li>Access your site from: <code>http://192.168.1.103/easyCart/public/</code></li>
            <li>Share this URL with others on your network</li>
            <li>They will need to enter username: <strong>anu</strong> and password: <strong>1234</strong></li>
        </ul>
    </div>
</body>
</html>
