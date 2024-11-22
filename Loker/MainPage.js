document.addEventListener("DOMContentLoaded", function () {

  // Dropdown toggle functionality
  const dropdownToggle = document.querySelector(".navbar .dropdown");
  if (dropdownToggle) {
    dropdownToggle.addEventListener("click", function () {
      const dropdownMenu = this.querySelector("ul");
      dropdownMenu.classList.toggle("show");
    });
  }

  // Handling search input visibility in mobile view
  const searchIcon = document.querySelector(".navbar .search i");
  const searchInput = document.querySelector(".navbar .search input");
  if (searchIcon && searchInput) {
    searchIcon.addEventListener("click", function () {
      searchInput.classList.toggle("show");
      if (searchInput.classList.contains("show")) {
        searchInput.focus();
      }
    });
  }

  // Smooth scroll to sections on navbar link click
  const navLinks = document.querySelectorAll(".navbar a");
  navLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const targetId = this.getAttribute("href").substring(1);
      const targetElement = document.getElementById(targetId);
      if (targetElement) {
        window.scrollTo({
          top: targetElement.offsetTop - 60, // Adjust for navbar height
          behavior: "smooth",
        });
      }
    });
  });

  // Handling filter inputs (e.g., search and dropdown)
  const filterInput = document.querySelector(".filters input");
  const filterSelect = document.querySelector(".filters select");
  if (filterInput || filterSelect) {
    if (filterInput) {
      filterInput.addEventListener("input", filterResults);
    }
    if (filterSelect) {
      filterSelect.addEventListener("change", filterResults);
    }
  }

  function filterResults() {
    const query = filterInput ? filterInput.value.toLowerCase() : "";
    const selectedOption = filterSelect ? filterSelect.value : "all";
    const items = document.querySelectorAll(".column ul li");

    items.forEach((item) => {
      const text = item.textContent.toLowerCase();
      const isMatch =
        text.includes(query) &&
        (selectedOption === "all" || item.dataset.category === selectedOption);
      item.style.display = isMatch ? "block" : "none";
    });
  }

  // Footer: Social icons hover effect
  const socialIcons = document.querySelectorAll(".footer-left .social-icons i");
  socialIcons.forEach((icon) => {
    icon.addEventListener("mouseenter", function () {
      this.style.color = "#0078d4"; // Highlight color
    });
    icon.addEventListener("mouseleave", function () {
      this.style.color = ""; // Reset color
    });
  });

  // Mobile view: Toggling visibility of menu items in navbar
  const menuToggle = document.querySelector(".navbar .menu-toggle");
  const menuItems = document.querySelector(".navbar .items");
  if (menuToggle && menuItems) {
    menuToggle.addEventListener("click", function () {
      menuItems.classList.toggle("show");
    });
  }

  // Job filtering logic
  const jobTypeFilter = document.getElementById("jobTypeFilter");
  const locationFilter = document.getElementById("locationFilter");
  const businessAreaFilter = document.getElementById("businessAreaFilter");
  const searchFilter = document.getElementById("searchFilter");
  const jobListings = document.getElementById("job-listings")?.getElementsByTagName("li");

  if (jobTypeFilter && locationFilter && businessAreaFilter && searchFilter) {
    // Add event listeners for each filter
    jobTypeFilter.addEventListener("change", filterJobs);
    locationFilter.addEventListener("change", filterJobs);
    businessAreaFilter.addEventListener("change", filterJobs);
    searchFilter.addEventListener("input", filterJobs);
  }

  function filterJobs() {
    if (!jobListings) return;

    // Get filter values
    const jobType = jobTypeFilter.value.toLowerCase();
    const location = locationFilter.value.toLowerCase();
    const businessArea = businessAreaFilter.value.toLowerCase();
    const searchTerm = searchFilter.value.toLowerCase();

    // Loop through job listings and filter them based on selected filters
    for (let i = 0; i < jobListings.length; i++) {
      const jobItem = jobListings[i];
      const jobText = jobItem.textContent.toLowerCase();

      const matchesJobType = jobType === "all types" || jobText.includes(jobType);
      const matchesLocation = location === "all locations" || jobText.includes(location);
      const matchesBusinessArea = businessArea === "" || jobText.includes(businessArea);
      const matchesSearch = searchTerm === "" || jobText.includes(searchTerm);

      // If job matches all selected filters, show it; otherwise, hide it
      if (matchesJobType && matchesLocation && matchesBusinessArea && matchesSearch) {
        jobItem.style.display = "block";
      } else {
        jobItem.style.display = "none";
      }
    }
  }

  let lowonganData = []; // Variabel untuk menyimpan data lowongan

  // Fungsi untuk mengambil data lowongan dari API dan menampilkan di tabel
  async function fetchLowongan() {
      try {
          const response = await fetch('http://localhost:8000/PHP/api/index.php');
          const data = await response.json();
          console.log('Data diterima:', data);
          lowonganData = data.lowongan || [];
          renderTable(lowonganData);
      } catch (error) {
          console.error('Error fetching data:', error);
      }
  }
  
  // Fungsi untuk menampilkan data lowongan di tabel
  function renderTable(data) {
      const tableBody = document.getElementById('lowongan-table-body');
      tableBody.innerHTML = '';
  
      data.forEach(lowongan => {
          const row = document.createElement('tr');
          row.innerHTML = `
              <td>${lowongan.id}</td>
              <td>${lowongan.title}</td>
              <td>${lowongan.description}</td>
              <td>${lowongan.location}</td>
              <td>
                  <button onclick="deleteLowongan(${lowongan.id})">Delete</button>
              </td>
          `;
          tableBody.appendChild(row);
      });
  }
  
  // Fungsi untuk menambahkan lowongan
  async function addLowongan(event) {
      event.preventDefault(); // Mencegah form dari submit default
  
      const title = document.getElementById('title').value;
      const description = document.getElementById('description').value;
      const location = document.getElementById('location').value;
  
      const newLowongan = { title, description, location };
  
      try {
          const response = await fetch('http://localhost:8000/PHP/api/index.php', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json',
              },
              body: JSON.stringify(newLowongan),
          });
  
          const data = await response.json();
          if (data.success) {
              fetchLowongan(); // Update tabel setelah menambah lowongan baru
          } else {
              console.error('Failed to add lowongan');
          }
      } catch (error) {
          console.error('Error adding lowongan:', error);
      }
  
      // Reset form
      document.getElementById('lowongan-form').reset();
  }
  
  // Fungsi untuk menghapus lowongan
  async function deleteLowongan(id) {
      try {
          const response = await fetch(`http://localhost:8000/PHP/api/index.php/lowongan/${id}`, {
              method: 'DELETE',
          });
  
          const data = await response.json();
          if (data.success) {
              fetchLowongan(); // Update tabel setelah menghapus lowongan
          } else {
              console.error('Failed to delete lowongan');
          }
      } catch (error) {
          console.error('Error deleting lowongan:', error);
      }
  }
  
  // Fungsi untuk mencari lowongan berdasarkan title atau location
  function filterLowongan() {
      const searchQuery = document.getElementById('search').value.toLowerCase();
      const filteredData = lowonganData.filter(lowongan => {
          return lowongan.title.toLowerCase().includes(searchQuery) ||
                 lowongan.location.toLowerCase().includes(searchQuery);
      });
      renderTable(filteredData);
  }
  
// Fungsi untuk mengurutkan data lowongan berdasarkan kolom
function sortLowongan(column) {
  // Tentukan arah pengurutan (ascending atau descending)
  const sortDirection = column === 'id' || column === 'title' || column === 'location' ? 'asc' : 'desc';

  lowonganData.sort((a, b) => {
      if (a[column] < b[column]) return sortDirection === 'asc' ? -1 : 1;
      if (a[column] > b[column]) return sortDirection === 'asc' ? 1 : -1;
      return 0;
  });

  renderTable(lowonganData);  // Render ulang tabel setelah pengurutan
}
});
