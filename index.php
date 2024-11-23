<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page 123</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="password"] {
            width: 95%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .checkbox {
            display: flex;
            align-items: center;
        }

        input[type="checkbox"] {
            margin-right: 10px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4caf50;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
            display: none;
        }
        .loading-overlay {
    display: none; /* Hidden by default */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.spinner {
    border: 5px solid #f3f3f3;
    border-top: 5px solid #3498db;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 10s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
.response-message {
    margin-top: 20px;
    font-size: 16px;
    color: #333;
    display: none;
}

.response-message.success {
    color: green;
}

.response-message.error {
    color: red;
}

    </style>
</head>

<body>
    <?php include 'navbar.php';?>
   <p>something</p>
    <div class="login-container">
        <div id="loginForm">
            <h2>Login</h2>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" >
                <p id="Uerror" class="error"></p>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" >
                <p id="Perror" class="error"></p>
            </div>
            <div class="form-group checkbox">
                <input type="checkbox" id="rememberMe" name="rememberMe">
                <label for="rememberMe">Remember me</label>
            </div>
            <button type="button" onclick="processData()">Login</button>
            <p id="error" class="error"></p>
</div>
        <div id="responseMessage" class="response-message"></div>
    </div>
    <div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div></div>
    <script>
    const processData = async ()=>{
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const errorElement = document.getElementById('error');
    const usernameErrorElement = document.getElementById('Uerror');
    const passwordErrorElement = document.getElementById('Perror');
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (username === '') {
        usernameErrorElement.textContent = 'Username is required.';
        usernameErrorElement.style.display = 'block';
        return;
    }

    if (password === '') {
        passwordErrorElement.textContent = 'Password is required.';
        passwordErrorElement.style.display = 'block';
        return;
    }
    errorElement.style.display = 'none';
    passwordErrorElement.style.display = 'none';
    usernameErrorElement.style.display = 'none';
    loadingOverlay.style.display = 'flex';
    try {
        setTimeout(() => {
            loadingOverlay.style.display = 'none';
            responseMessage.style.display = 'block';
        }, 2000); 
        const response = await fetch('processLogin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password, rememberMe }),
        });
        const data = await response.json();
        if (response.ok) {
            responseMessage.textContent = data.message || 'Authentication successful!';
            responseMessage.classList.add('success');
            responseMessage.classList.remove('error');
        } else {
            responseMessage.textContent = data.message || 'Authentication failed.';
            responseMessage.classList.add('error');
            responseMessage.classList.remove('success');
        }
    } catch (error) {
        responseMessage.textContent = 'An error occurred. Please try again.';
        responseMessage.classList.add('error');
        responseMessage.classList.remove('success');
    }
};
    </script>
</body>

</html>