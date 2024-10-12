// Handle navigation and dynamic content loading
const contentDiv = document.getElementById("content");

function loadContent(url) {
  fetch(url)
    .then(response => response.text())
    .then(data => {
      contentDiv.innerHTML = data;
    })
    .catch(error => {
      console.error("Error loading content:", error);
    });
}

// Example: Load the data entry form when the "Data Entry" link is clicked
document.querySelector("nav a[href='data_entry.html']").addEventListener("click", () => {
  loadContent("data_entry.html");
});

// Handle form submissions for registration
document.getElementById("register-form")?.addEventListener("submit", function(event) {
  event.preventDefault();

  // Validate form data
  const username = document.getElementById("username").value;
  const email = document.getElementById("email").value;
  const password = document.getElementById("password").value;

  if (username.trim() === "") {
    alert("Please enter a username.");
    return;
  }

  if (email.trim() === "") {
    alert("Please enter an email address.");
    return;
  }

  if (password.trim() === "") {
    alert("Please enter a password.");
    return;
  }

  // Submit form data to the server using AJAX
  fetch("register.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: `username=${username}&email=${email}&password=${password}`
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert(data.message);
        window.location.href = "login.html";
      } else {
        alert(data.message);
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

  // Validate form data
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;

  if (username.trim() === "") {
    alert("Please enter a username.");
    return;
  }

  if (password.trim() === "") {
    alert("Please enter a password.");
    return;
  }

  // Submit form data to the server using AJAX
  fetch("login.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: `username=${username}&password=${password}`
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert(data.message);
        window.location.href = "index.html";
      } else {
        alert(data.message);
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

  // Validate form data
  const date = document.getElementById("date").value;
  const foodWaste = document.getElementById("food_waste").value;
  const recycledWater = document.getElementById("recycled_water").value;
  const solidWaste = document.getElementById("solid_waste").value;
  const wetWaste = document.getElementById("wet_waste").value;

  // Submit form data to the server using AJAX
  fetch("data_entry.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: `date=${date}&food_waste=${foodWaste}&recycled_water=${recycledWater}&solid_waste=${solidWaste}&wet_waste=${wetWaste}`
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert(data.message);
        // Refresh the reports page
        loadContent("reports.html");
      } else {
        alert(data.message);
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

  const dateRange = document.querySelector('input[name="date_range"]:checked');
  const monthOption = document.querySelector('input[name="month"]:checked');
  let reportData = {};

  if (dateRange) {
    const fromDate = document.getElementById("from_date").value;
    const toDate = document.getElementById("to_date").value;
    reportData = {
      type: 'date_range',
      from_date: fromDate,
      to_date: toDate
    };
  } else if (monthOption) {
    const month = document.getElementById("month").value;
    reportData = {
      type: 'month',
      month: month
    };
  } else {
    alert("Please select a date range or month.");
    return;
  }

  // Submit report request to the server
  fetch("generate_report.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(reportData)
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Clear previous report data
        const reportTableBody = document.querySelector("#reports-table tbody");
        reportTableBody.innerHTML = ""; // Clear previous rows

        // Populate table with report data
        let totalFoodWaste = 0;
        let totalRecycledWater = 0;
        let totalSolidWaste = 0;
        let totalWetWaste = 0;

        data.reports.forEach(report => {
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
          totalFoodWaste += report.food_waste;
          totalRecycledWater += report.recycled_water;
          totalSolidWaste += report.solid_waste;
          totalWetWaste += report.wet_waste;
        });

        // Update totals in the footer
        document.getElementById("total_food_waste").innerText = totalFoodWaste;
        document.getElementById("total_recycled_water").innerText = totalRecycledWater;
        document.getElementById("total_solid_waste").innerText = totalSolidWaste;
        document.getElementById("total_wet_waste").innerText = totalWetWaste;
      } else {
        alert(data.message);
      }
    })
    .catch(error => {
      console.error("Error generating report:", error);
      alert("Error generating report. Please try again later.");
    });
});