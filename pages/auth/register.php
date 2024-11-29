<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <link href="../../../public/css/tailwind.css" rel="stylesheet">
    <style>

.login-container {
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    margin-top: 20px;         /* Space between navbar and form */
}


.form-group {
    margin-bottom: 15px;
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
<?php include __DIR__.'../../../components/navbar.php';?>
    <div class="login-container max-w-lg mx-auto bg-gray-600">
        <div id="loginForm" class="bg-grey-400">
            <h2 class="mb-5 text-white text-2xl">Sign Up</h2>
            <div class="form-group">
                <label class="text-white font-bold" for="username">Username:</label>
                <input type="text" id="username" class="text-primaryColor mt-3" placeholder="Enter a username" name="username" onkeyup="checkUsernameExists(this.value)">
                <p id="Uerror" class="error"></p>
            </div>
            <div class="form-group">
                <label class="text-white font-bold" for="username">First Name:</label>
                <input type="text" id="Fname" class="text-primaryColor mt-3" placeholder="Enter your first name" name="username" >
                <p id="Ferror" class="error"></p>
            </div>
            <div class="form-group">
                <label class="text-white font-bold" for="username">Last Name:</label>
                <input type="text" id="Lname" class="text-primaryColor mt-3" placeholder="Enter your last name" name="username" >
                <p id="Lerror" class="error"></p>
            </div>
            <div class="form-group">
                <label class="text-white font-bold" for="email">Email:</label>
                <input type="email" id="email" class="text-primaryColor mt-3" name="email" placeholder="Enter your email" onkeyup="checkIfEmailPresent(this.value)" >
                <p id="Eerror" class="error"></p>
            </div>
            <div class="form-group">
                <label class="text-white font-bold" for="username">Address:</label>
                <input type="text" id="address" class="text-primaryColor mt-3" placeholder="Enter your address" name="address"required >
                <p id="Aerror" class="error"></p>
            </div>
            <div class="form-group">
                <label class="text-white font-bold" for="username">Date of Birth:</label>
                <input type="date" id="DOB" class="text-primaryColor mt-3" name="DOB" required >
                <p id="Derror" class="error"></p>
            </div>
            <div class="form-group">
                <label class="text-white font-bold" for="password">Password:</label>
                <input type="password" id="password" class="text-primaryColor mt-3" name="password" placeholder="Enter your password" onkeyup="checkPasswordExpression(this.value)" >
                <p id="Perror" class="error text-1xl text-red-500"></p>
            </div>
            <div class="form-group">
                <label class="text-white font-bold" for="password">Confirm Password:</label>
                <input type="password" id="confirmPassword" class="text-primaryColor mt-3" placeholder="Confirm your password" name="confirmPassword"  >
                <p id="CPerror" class="error  "></p>
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
    xhr.open("GET", "../../data/processes/processSignup.php?email=" + encodeURIComponent(email), true);
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
    xhr.open("GET", "../../data/processes/processSignup.php?username=" + encodeURIComponent(username), true);

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
        const response = await fetch('../../data/processes/processSignup.php', {
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
    <script src="../../../assets/js/scripts.js"></script>
</body>
</html>