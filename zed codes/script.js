function showForm(formId) {
    document.querySelectorAll(".form").forEach(form => {
        form.classList.remove("active");
    });

    document.getElementById(formId).classList.add("active");
}


// REGISTRATION

document.getElementById("register").addEventListener("submit", async function(e) {
    e.preventDefault();

    const message = document.getElementById("register_message");

    const full_name = document.getElementById("full_name").value.trim();
    const username = document.getElementById("username").value.trim();
    const email = document.getElementById("register_email").value.trim();
    const password = document.getElementById("register_password").value;

    message.textContent = "Creating account...";

    try {
        const response = await fetch("/Nue-Nue/zed%20codes/backened/register.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                full_name,
                username,
                email,
                password
            })
        });

        const result = await response.json();

        message.textContent = result.message;

        if (result.success) {
            document.getElementById("register").reset();
        }

    } catch (error) {
        console.error("Registration error:", error);
        message.textContent = "Unable to connect to the server.";
    }
});


// LOGIN

document.getElementById("signin").addEventListener("submit", async function(e) {
    e.preventDefault();

    const message = document.getElementById("login_message");

    const email = document.getElementById("login_email").value.trim();
    const password = document.getElementById("login_password").value;

    message.textContent = "Checking account...";

    try {
        const response = await fetch("/Nue-Nue/zed%20codes/backened/login.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ email, password })
        });

        const text = await response.text();

        console.log("HTTP status:", response.status);
        console.log("Server response:", text);

        let result;

        try {
            result = JSON.parse(text);
        } catch {
            message.textContent = "PHP did not return valid JSON. Check the Console.";
            return;
        }

        message.textContent = result.message;

        if (result.success) {
            console.log("Logged in as:", result.username);
        }

    } catch (error) {
        console.error("Connection error:", error);
        message.textContent = "Could not reach the PHP file. Check the file path.";
    }
});