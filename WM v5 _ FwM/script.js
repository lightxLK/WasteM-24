document.addEventListener("DOMContentLoaded", () => {
    // Check login status
    fetch("session.php")
        .then(response => response.json())
        .then(data => {
            const usernameDisplayGreeting = document.getElementById("username-display-greeting");
            const logoutBtn = document.getElementById("logout-btn");
            const loginBtn = document.getElementById("login-btn");
            const userGreeting = document.getElementById("user-greeting");
            const usernameDisplay = document.getElementById("username-display");

            if (data.loggedIn) {
                usernameDisplayGreeting.innerText = data.username; // Set the greeting username
                usernameDisplay.innerText = data.username; // Set the username next to the logout button
                usernameDisplay.style.display = "inline"; // Ensure username is displayed inline
                userGreeting.style.display = "block";       // Show greeting
                logoutBtn.style.display = "inline";         // Show Logout button
                loginBtn.style.display = "none";            // Hide Login button
            } else {
                userGreeting.style.display = "none";      // Hide greeting
                usernameDisplay.style.display = "none";   // Hide username display
                logoutBtn.style.display = "none";         // Hide Logout button
                loginBtn.style.display = "inline";       // Show Login button
            }
        })
        .catch(error => console.error("Error fetching session:", error));

    // Handle form submissions for registration
    document.getElementById("register-form")?.addEventListener("submit", function(event) {
        event.preventDefault();
        const formData = new FormData(this); // Collect form data

        fetch("register.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                window.location.href = "login.html"; // Redirect to login page
            }
        })
        .catch(error => {
            console.error("Error registering user:", error);
            alert("Error registering user. Please try again later.");
        });
    });

    // Handle form submissions for login
    document.getElementById("login-form")?.addEventListener("submit", function(event) {
        event.preventDefault();
        const formData = new FormData(this); // Collect form data

        fetch("login.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                window.location.href = "data_entry.html"; // Redirect to data entry page
            }
        })
        .catch(error => {
            console.error("Error logging in:", error);
            alert("Error logging in. Please try again later.");
        });
    });

    // Handle form submissions for data entry
    document.getElementById("data-entry-form")?.addEventListener("submit", function(event) {
        event.preventDefault();
        const formData = new FormData(this); // Collect form data

        fetch("data_entry.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                this.reset(); // Reset the form upon successful submission
            }
        })
        .catch(error => {
            console.error("Error submitting data:", error);
            alert("Error submitting data. Please try again later.");
        });
    });

    // Handle form submissions for reports
    document.getElementById("reports-form")?.addEventListener("submit", function(event) {
        event.preventDefault();
        const reportData = gatherReportData();

        if (!reportData) {
            alert("Please select a date range or month.");
            return;
        }

        fetch("generate_reports.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(reportData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayReports(data.reports);
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error("Error generating report:", error);
            alert("Error generating report. Please try again later.");
        });
    });

    // Function to gather report data based on selected options
    function gatherReportData() {
        const dateRange = document.querySelector('input[name="date_range"]:checked');
        const monthOption = document.querySelector('input[name="month"]:checked');

        if (dateRange) {
            const fromDate = document.getElementById("from_date").value;
            const toDate = document.getElementById("to_date").value;
            return {
                type: 'date_range',
                from_date: fromDate,
                to_date: toDate
            };
        } else if (monthOption) {
            const month = document.getElementById("month").value;
            return {
                type: 'month',
                month: month
            };
        }
        return null;
    }

    // Function to display reports in the table
    function displayReports(reports) {
        const reportTableBody = document.querySelector("#reports-table tbody");
        reportTableBody.innerHTML = ""; // Clear previous rows

        let totalFoodWaste = 0;
        let totalRecycledWater = 0;
        let totalSolidWaste = 0;
        let totalWetWaste = 0;

        reports.forEach(report => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${report.date}</td>
                <td>${report.food_waste}</td>
                <td>${report.recycled_water}</td>
                <td>${report.solid_waste}</td>
                <td>${report.wet_waste}</td>
            `;
            reportTableBody.appendChild(row);

            // Calculate totals
            totalFoodWaste += parseFloat(report.food_waste);
            totalRecycledWater += parseFloat(report.recycled_water);
            totalSolidWaste += parseFloat(report.solid_waste);
            totalWetWaste += parseFloat(report.wet_waste);
        });

        // Update totals in the footer
        document.getElementById("total_food_waste").innerText = totalFoodWaste;
        document.getElementById("total_recycled_water").innerText = totalRecycledWater;
        document.getElementById("total_solid_waste").innerText = totalSolidWaste;
        document.getElementById("total_wet_waste").innerText = totalWetWaste;
    }

    // Function to log out the user
    window.logout = function() {
        fetch("logout.php")
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to homepage after logout
                    window.location.href = "index.html"; 
                } else {
                    alert("Logout failed. Please try again.");
                }
            })
            .catch(error => {
                console.error("Error logging out:", error);
                alert("Error logging out. Please try again later.");
            });
    };
});