<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <link rel="stylesheet" href="navbar.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    display: flex;
    justify-content: center;
    align-items: flex-start;  /* Align the content from the top */
    min-height: 100vh;        /* Make sure the body fills the viewport */
    margin: 0;
    padding-top: 60px;        /* Space for the navbar */
}

.login-container {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    margin-top: 20px;         /* Space between navbar and form */
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
input[type="password"],
input[type="date"],
input[type="email"] {
    width: 100%;  /* Make inputs take full width */
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box; /* Ensure padding doesn't affect width */
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
    display: none;
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
    animation: spin 1s linear infinite;  /* Faster rotation */
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
    <div class="login-container">
        <div id="loginForm">
            <h2>Sign Up</h2>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" onkeyup="checkUsernameExists(this.value)">
                <p id="Uerror" class="error"></p>
            </div>
            <div class="form-group">
                <label for="username">First Name:</label>
                <input type="text" id="Fname" name="username" >
                <p id="Ferror" class="error"></p>
            </div>
            <div class="form-group">
                <label for="username">Last Name:</label>
                <input type="text" id="Lname" name="username" >
                <p id="Lerror" class="error"></p>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" onkeyup="checkIfEmailPresent(this.value)" >
                <p id="Eerror" class="error"></p>
            </div>
            <div class="form-group">
                <label for="username">Address:</label>
                <input type="text" id="address" name="address"required >
                <p id="Aerror" class="error"></p>
            </div>
            <div class="form-group">
                <label for="username">Date of Birth:</label>
                <input type="date" id="DOB" name="DOB" required >
                <p id="Derror" class="error"></p>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" onkeyup="checkPasswordExpression(this.value)" >
                <p id="Perror" class="error"></p>
            </div>
            <div class="form-group">
                <label for="password">Confirm Password:</label>
                <input type="password" id="confirmPassword" name="confirmPassword"  >
                <p id="CPerror" class="error"></p>
            </div> 
            <button type="button" onclick="processData()">Login</button>
            <p id="error" class="error"></p>
</div>
        <div id="responseMessage" class="response-message"></div>
    </div>
    <div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div></div>
    <script>
    const checkPasswordExpression = (password)=>{
        const passwordElement = document.getElementById('Perror');
        const passwordRegex = /^(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/;
        if(passwordRegex.test(password) == false){
            passwordElement.innerText = 'The password must be 8 characters long with a special character';
            passwordElement.style.display = 'block';
        }else{
            passwordElement.style.display = 'none';
        }
    }
    const checkIfEmailPresent = (email)=>{
        const emailError = document.getElementById("Eerror");
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email.length === 0) {
            emailError.innerHTML = "";
        return;
    }
    if(emailRegex.test(email) == false)
    {
        emailError.innerHTML = "This must be a valid email";
    }else{
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "processSignup.php?email=" + encodeURIComponent(email), true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            if(xhr.responseText == "Yes"){
                emailError.innerHTML = "This email is available";
                emailError.style.display = 'block';
                emailError.style.color = 'green';
            }else{
                emailError.innerHTML = "This email is not available";
                emailError.style.display = 'block';
                emailError.style.color = 'red';
            }
            
        }
    };
    xhr.send();
    }
}
    const checkUsernameExists = (username)=>{
        const usernameError = document.getElementById("Uerror");
        if (username.length === 0) {
            usernameError.innerHTML = "";
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "processSignup.php?username=" + encodeURIComponent(username), true);

    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            if(xhr.responseText == "Yes"){
                usernameError.innerHTML = "This Username is available";
                usernameError.style.display = 'block';
                usernameError.style.color = 'green';
            }else{
                usernameError.innerHTML = "This Username is not available";
                usernameError.style.display = 'block';
                usernameError.style.color = 'red';
            }
            
        }
    };
    xhr.send();
    } 

    const processData = async ()=>{
    const username = document.getElementById('username').value.trim();
    const firstName = document.getElementById('Fname').value.trim();
    const lastName = document.getElementById('Lname').value.trim();
    const email = document.getElementById('email').value.trim();
    const address = document.getElementById('address');
    const dob = document.getElementById('DOB');
    const password = document.getElementById('password').value.trim();
    const confirmPassword = document.getElementById('confirmPassword').value.trim();
    const errorElement = document.getElementById('error');
    const usernameErrorElement = document.getElementById('Uerror');
    const passwordErrorElement = document.getElementById('Perror');
    const confirmPasswordErrorElement = document.getElementById('CPerror');
    const emailErrorElement = document.getElementById('Eerror');
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
        let formData = {"username":username,"firstName":firstName,"email":email,"lastName":lastName,"password":password,"address":address.value,"DOB":dob.value};
        const response = await fetch('processSignup.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData),
        });
        const data = await response.json();
        if (response.ok) {
            responseMessage.classList.add('success');
            responseMessage.classList.remove('error');
            responseMessage.textContent = data.message || 'Authentication successful!';
            window.location.href = 'index.php'; 
          
        } else if(response.status ==  500) {
            responseMessage.classList.add('error');
            responseMessage.classList.remove('success');
            responseMessage.textContent = data.message || 'Authentication failed.';
     
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